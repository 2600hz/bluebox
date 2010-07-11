<?php
/**
 * Description of AsteriskDoc
 *
 * @author dschreiber
 */
class AsteriskDoc {
    /**
     * An multi-dimensional array of strings that will be used in generating hte dialplan for a specific number/destination
     * @var array
     */
    //public $queuedCommands;

    protected $currentFilename = NULL;

    protected $currentContext = NULL;

    /**
     * For dialplan, current section we are working in
     * @var string
     */
    protected $currentNumber = NULL;

    /**
     * Multi-dimensional array of [filename][context] that stores the * config that's been loaded already
     * @var array
     *
     * TODO: This should probably be private and accessed via helpers, no?
     */
    public $fileCache;

    public function getCurrentNumber()
    {
        return $this->currentNumber;
    }

    public function setCurrentNumber($currentNumber)
    {
        $this->currentNumber = $currentNumber;
    }

    public function getCurrentFilename()
    {
        return $this->currentFilename;
    }

    public function getCurrentContext()
    {
        return $this->currentContext;
    }

    /**
     * Set the internal pointer for what sections we're modifying
     */
    public function &setPosition($filename, $context, $number = NULL, $autoload = TRUE) {
        $this->currentFilename = $filename;
        $this->currentContext = $context;

        if ($number) {
            $this->currentNumber = $number;
        } else {
            $this->currentNumber = '_X.';
        }

        // the general section is a special case
        if ($context == 'general') {
            $this->fileCache[$filename][$context] = array();
            return array();
        }

        // Do we already have this context in memory? If so, do nothing. Otherwise, try and load it, or if that fails (or autoloading is off), create it.
        if (!isset($this->fileCache[$filename][$context])) {
            if ($autoload) {
                // Try to load the existing context via the driver (psuedo-autoload)
                $this->fileCache[$filename][$context] = Telephony::getDriver()
                                                                ->load(array(
                                                                            'filename' => $filename,
                                                                            'context' => $context
                                                                            )
                                                                      );
            }

            // If the context is still empty, initialize it
            if (!isset($this->fileCache[$filename][$context]) or !($this->fileCache[$filename][$context])) {
                // Nothing loaded? Initialize a new section then.
                $this->fileCache[$filename][$context] = array();
            }
        }

        return $this->fileCache[$filename][$context];
    }

    public function get($filename, $context = NULL)
    {
        if ($context) {
            if (isset($this->fileCache[$filename]['context_' . $context])) {
                return $this->fileCache[$filename]['context_' . $context];
            } else {
                return FALSE;
            }
        }
        if (isset($this->fileCache[$filename])) {
            return $this->fileCache[$filename];
        } else {
            return FALSE;
        }
    }

    public function update($filename, $context, $var, $value, $add = TRUE) {
        $cleanVar = str_replace('/', '\/', preg_quote($var));
        if (is_string($add)) {
            $add = "${var}=${add}";
        }
        $this->updateRegex($filename, $context, "/${cleanVar}[\s]*=>?.*/", "${var}=${value}", $add);
    }

    public function append($filename, $context, $var, $value, $add = TRUE) {
        $cleanVar = str_replace('/', '\/', preg_quote($var));
        $clearnValue = str_replace('/', '\/', preg_quote($value));
        if (is_string($add)) {
            $add = "${var}=${add}";
        }
        $this->updateRegex($filename, $context, "/${cleanVar}[\s]*=>?[\s]*${clearnValue}/", "${var}=${value}", $add);
    }

    public function updateRegex($filename, $contextName, $search, $replace, $add = TRUE)
    {
        /*$options = array(
            'filename' => $filename,
            'context' => $context
        );
        /$this->load($options);*/

        $context = & $this->fileCache[$filename][$contextName];
        $found = FALSE;

        // the general context is a special case
        if ($contextName == 'general') {
            $lineParts = explode('=', $replace, 2);
            if (!empty($lineParts) && count($lineParts) == 2 && !empty($lineParts[1])) {
                $ami = Telephony::getDriver()->ami;
                $ami->queueConfigUpdate($filename, 'Delete', $contextName, $lineParts[0], $lineParts[1], array(
                    'match' =>  $lineParts[1],
                    'ignoreResponse' => AsteriskManager::AMI_DEL_FAIL1
                ));
            } else {
                Kohana::log('error', 'Unable to delete \'' . $line . '\' for [' . $context . '] in ' . $filename);
            }
        } else {
            // Is there anything loaded in memory to look at? If not, assume new file
            if (!empty($context) && is_array($context)) {
                // Cycle through all lines in $fileCache[$filename][$context] and run the regex
                foreach($context as $k => $line) {
                    // Look for matches and replace them accordingly
                    $context[$k] = preg_replace($search, $replace, $line, -1, $count);
                    if (!empty($count)) {
                        $found = TRUE;
                    }
                }
            }
        }

        // if we did not locate what we are looking for add it, as this function is an "add or update"
        if (!$found && $add !== FALSE) {
            if (is_string($add)) {
                $context[]= $add;
            } else {
                $context[] = $replace;
            }
        }
    }

    /**
     * Create a new Asterisk context. This creates a context within a file, in the form [contextName].
     * It also sets the internal position pointers to the context that was just created. You can optionally
     * pass in an extension number to set the position to that extension number as well.
     * You may pass in an options list, including the replace option key which will replace an existing context's contents
     * of the same name.
     *
     * This is the most basic building block of Asterisk dialplan creation, and is used in many other places.
     *
     * @param string $filename
     * @param string $contextName
     * @param string $extensionNumber
     * @param array $options
     * @return self
     */
    public function createContext($filename, $contextName, $extensionNumber = NULL, array $options = array())
    {
        // STEP 1: Store the context and extension, for later use, and load that context into memory if possible
        // NOTE: The last parameter toggles the autoload flag so we don't bother auto-loading if we know we are going to clobber this context anyway
        $context = &$this->setPosition($filename, $contextName, $extensionNumber, !isset($options['replace']));

        // If the $options['replace'] option is set, clobber existing filename/context entries.
        if (isset($options['replace'])) {
            $context = array();    // This effectively deletes the current context, too, if nothing gets added here
        } else {

        }

        kohana::log('debug', "Set current filename to $filename, context to $contextName and extension to " . $this->currentNumber);
        return $this;
    }

    public function deleteContext($filename, $contextName) {
        $this->createContext($filename, $contextName, NULL, array('replace' => TRUE));
    }

    /**
     * This will add commands to the current, active dialplan filename/context/extension, as set by setPosition() or during
     * a context creation command (createConetxt())
     *
     * This is the second most basic building block of Asterisk dialplan creation, and is used in many other places.
     *
     * @param string $command
     * @param string $priorityName Optional
     * @param array $options
     */
    public function add($command, $priorityName = NULL, array $options = array())
    {
        // TODO: If REPLACE is true, set a flag to search/replace this option somehow
        
        $context = $this->currentContext;
        $filename = $this->currentFilename;
        $number = $this->currentNumber;

        if (!$context or !$filename) {
            Kohana::log('error', 'Can\'t add to a non-existant context/filename - use setPosition() first! (When trying to add ' . $command . ')');
            return FALSE;
        }

        if (is_string($priorityName)) {
            $priorityName = 'n(' . $priorityName . ')';
        } else if (!is_int($priorityName)){
            $priorityName = 'n';
        }

        // STEP 3: Add to the context. Prefix with a NoOp() on any context that's empty automagically.
        if ((!isset($this->fileCache[$filename][$context]) or (count($this->fileCache[$filename][$context]) == 0)) && $priorityName != 1) {
            $this->fileCache[$filename][$context] = array('exten = ' . $number . ',1,NoOp');    // Add a NoOp at the top of all contexts
        }
        
        $this->fileCache[$filename][$context][] = 'exten = ' . $number . ',' . $priorityName . ',' . $command;
    }


    /***********************************
     * DIALPLAN SPECIFIC FUNCTIONALITY *
     ***********************************/
    /**
     * This will create an entry in the extensions listing context to route a call to a specific number/destination
     *
     *      [extensions_1]
     *      exten => 3000.,1,NoOp
     *
     * From there add() will then further populate the priorities.
     * 
     * If $extensionNumber is not passed in, we'll default to _X., like:
     *      exten => _X.,1,NoOp
     */
    public function createDialplanExtension($contextId, $numberId, $extensionNumber = NULL) {
        // Make sure the extensions list for this context exists
        $this->createContext('extensions.conf', 'extensions_' . $contextId, $extensionNumber);
        
        // Delete any existing references to this particular extension number in the extensions list
        $this->deleteDialplanExtension($contextId, $extensionNumber);
        $this->add('NoOp', 1, array('replace' => TRUE)); // Add a NoOp at the top of all numbers
        $this->add('GoSub(number_' . $numberId . ',${EXTEN},1)', NULL, array('replace' => TRUE)); // Replace nay matching extension definitions
        $this->add('Return');
    }

    public function deleteDialplanExtension($contextId, $extensionNumber) {
        // Despite this saying "create", by leaving a context empty, it will be deleted.
        //$this->createContext('extensions.conf', 'extensions_' . $contextId, $number);

        // Go get a copy of this context (load it if necessary) prior to attempting to delete
        $context =& $this->setPosition('extensions.conf', 'extensions_' . $contextId, $extensionNumber);

        // Delete any references to this destination/extensions
        //$exten = $extensionNumber;
        $regex = '/^exten[\s]*=?[\s]*' .preg_quote($extensionNumber) .',.*/';
        $context = preg_replace($regex, '', $context, -1, $count);
        /*if (preg_match('/^exten[\s]*=>?[\s]*(' .$exten .',.*)/', $cache, $matches)) {
            if ((count($matches) > 1) and ($matches[1] != '')) {
                $ami->queueConfigUpdate($contextFile, 'Delete', $context, 'exten', $matches[1], array('match' =>  $matches[1]));
            }
        }*/
    }

    /**
     * This function is dual-purpose. It routes a number to a destination by:
     * - creating an entry in the extension list [extensions_X] so this destination can be reached
     * - creating a placeholder for the destination's commands in a [number_X] context, if it doesn't already exist
     * Since the placeholder for the number_X context is created last, you are automatically left "positioned"
     * at the right place for using add() to continue adding commands for the destination's dialplan.
     * 
     * @param integer $contextId
     * @param string $extensionNumber
     * @param integer $numberId
     * @return boolean
     */
    public function createDestination($contextId, $numberId, $extensionNumber) {
        // Create a route to this destination
        $this->createDialplanExtension($contextId, $numberId, $extensionNumber);   // This just makes sure the context exists and sets our current number

        // Create placeholder for dialplan extension & set to current position
        // Create the dummy placeholder for the destination itself (and delete any existing destination, forcing it to be rebuilt, if it exists already)
        // We assume the next logical thing to do is add things to the extension itself that we just pointed at, so
        // set create that extension if it doesn't exist & set the position for it
        $this->createContext('extensions.conf', 'number_' . $numberId, NULL, array('replace' => TRUE));
    }

    // Delete the reference to an extension. Unlike what createDestiantion() does, this DOES NOT
    // delete the destination's dialplan (in case it is in use by other contexts). You must do that manually
    // if you are sure that nobody else is utilizing this number!
    public function deleteDestination($contextId, $extensionNumber, $numberId) {
        // Remove any references in the extension list for this destination
        $this->deleteDialplanExtension($contextId, $extensionNumber);

    }

    /**
     * Creates a context that you can route calls to initially. Takes care of adding the global event hooks/conditions
     * at the top of the context and then does a GoTo to the context's destinations list
     * @param integer $contextId
     */
    public function createRoutableContext($contextId) {
        // TODO: ADD SKIP FUNCTIONALITY HERE. Don't recreate this on the same run if we've already created this context, that's dumb.
        $this->createContext('extensions.conf', 'context_' . $contextId, NULL, array('replace' => TRUE));

        // Add global event hooks
        dialplan::start('context_' . $contextId);
        // TODO: Context gets clobbered during dialplan start/end, so let's set it again
        //self::
        $this->add('GoSub(extensions_' . $contextId . ',${EXTEN},1)');

        // Put end of dialplan stuff
        dialplan::end('context_' . $contextId);

        // TODO: Context gets clobbered again. Set it up again.
        $this->add('Hangup()');

    }

    public function reset()
    {
        $this->currentFilename = NULL;
        $this->currentContext = NULL;
        $this->currentNumber = NULL;
        $this->fileCache = array();
        return TRUE;
    }

}
