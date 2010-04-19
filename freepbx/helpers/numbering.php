<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * numbering.php - Phone number reservation helper
 *
 * This class assists in displaying what numbers are available, or reserved, in a consistent manner (look & feel)
 *
 *
 * @author K Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage Core
 */
class numbering extends form
{
    protected static $fieldCount = 1;

    protected static $numbersCache = array();

    /**
     * This will generate a multi-select (by default) of all numbers in a pool
     * or list of pools and select those assigned to a classes foreign id.
     * The values of the select option will be the number id.
     *
     * This dropdown can also be configured to be a select a single number example:
     *     echo numbering::dropdown(array(
     *          'classType' => 'AutoAttendantNumber',
     *          'multiple' => FALSE,
     *          'listAssigned' => FALSE,
     *          'optGroups' => FALSE
     *     ));
     * and if you would like to have a 'null select' option add to the array:
     *    nullOption => 'Select'
     *
     *
     * Additional Data Options:
     *  classType   = a single pool type to list or an array of types, if empty
     *                    all are used.
     *  optGroups   = Render the select options in optgroups
     *                    by assigned/unassigned
     *  nullOption  = If this is a string then it is used as the '0' option,
     *                    or if false then no such option will exist
     * listAssigned = Include the assigned number in the list
     *
     * NOTE: you can not change the name of this dropdown
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        A foreign id of the class type
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function dropdown($data, $selected = NULL, $extra = '') {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('classType' => $data);
        }

        // if we dont have a class type then we fail
        if (empty($data['classType'])) {
            kohana::log('error', 'You can not render the numbering::assigndropdown without a class type!');
            return FALSE;
        }

        // add in all the defaults if they are not provided
        $data += array(
            'multiple' => 'multiple',
            'size' => 6,
            'id' => 'assign' .$data['classType'],
            'optGroups' => TRUE,
            'nullOption' => FALSE,
            'listAssigned' => TRUE
        );
        $data['name'] = '_numbers[assign][]';

        // if multiple is empty then unset it
        if (empty($data['multiple'])) {
            unset($data['multiple']);
        }

        // append or insert the class
        arr::update($data, 'class', ' numbers_dropdown');

        // get any numbers already assigned to the is dropdown
        $options['Assigned'] = self::getAssignedNumbers($data['classType'], $selected);

        // get all the unassigned number of the class types in unassignedTypes
        $options['Unassigned'] = self::getUnassignedNumbersByPool($data['classType']);
        unset($data['classType']);

        // render a null option if its been set in data
        if (!empty($data['nullOption'])) {
            array_unshift($options['Unassigned'], __($data['nullOption']));
        }
        unset($data['nullOption']);

        // the dropdown expects the assigned array in reverse order
        $selected = array_flip($options['Assigned']);

        // see if there are any posts for this dropdown
        if($post = arr::parse_str($data['name'], $_POST))
            $selected += $post;

        // remove any attributes the dropdown helper will not understand
        $localAttr = array('additionalTypes', 'class_type');
        $data = array_diff_key($data, array_flip($localAttr));

        // if we are not listing the assigned numbers in this select
        if (!$data['listAssigned']) {
            $options['Assigned'] = array();
        }

        // if we are rendering this as optGroups ensure the markup will be valid
        // otherwise merge the array
        if ($data['optGroups']) {
            // remove the empty optgroups (illegal markup to have a empty group)
            if (empty($options['Assigned'])) unset($options['Assigned']);
            if (empty($options['Unassigned'])) unset($options['Unassigned']);
        } else {
            // Array merge re-indexs the keys so manually merge
            $assigned = $options['Assigned'];
            $options = $options['Unassigned'];
            foreach($assigned as $key => $number) {
                $options[$key] = $number;
            }
        }
        unset($data['optGroups']);

        // use kohana helper to generate the markup
        $layout = form::dropdown($data, $options, $selected, $extra);
        $layout .= form::hidden('containsAssigned[]', $data['name']);

        return $layout;
    }

    /**
     * This will generate a list of all assigned numbers in a pool or list of
     * pools.  The values of the select option will be the number id.
     *
     * Additional Data Options:
     *  classType  = a single pool type to list or an array of types, if empty
     *                   all are used.
     *  optGroups  = Render the select options in optgroups by pool type
     *  useNames   = Attemps to use the assigned destination name when avaliable
     *  nullOption = If this is a string then it is used as  the '0' option, or
     *                   if false then no such option will exist
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        option key that should be selected by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function numbersDropdown($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'None',
            'classType' => FALSE,
            'optGroups' => TRUE,
            'useNames' => FALSE,
            'contextAware' => FALSE
        );

        // if types was just a string then make it an array
        if ( !empty($data['classType']) && !is_array($data['classType']))
        {
            $data['classType'] = array($data['classType']);
        }

        // append or insert the class
        arr::update($data, 'class', ' destination_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption'])) {
            $options = array(0 => __($data['nullOption']));
        } else {
            $options = array();
        }

        // check if we should attempt to fill the selected
        $selected = self::_attemptRePopulate($data['name'], $selected);

        // foreach of the number types build a optgroup
        $types = self::getPoolTypes();
        foreach($types as $type) {
            // skip this we have a list of number types to include and not this
            if (is_array($data['classType']) && !in_array($type, $data['classType'])) continue;

            if (isset(self::$numbersCache[$type])) {
                $assignedInPool = self::$numbersCache[$type];
                if (isset($assignedInPool[$selected .'" class="' .$type])) {
                    $selected = $selected .'" class="' .$type;
                }
            } else {
                // get a list of all the assigned numbers in this pool
                $assignedInPool = self::getAssignedNumbersByPool($type);

                // if there are no assigned numbers then skip this pool
                if (empty($assignedInPool)) continue;

                // make a user friendly name for the pool name
                $poolName = str_replace('Number', '', $type);

                // TODO: This is a hack to handle numbers that exist in multiple contexts
                if ($data['contextAware']) {
                    foreach ($assignedInPool as $key => $number) {
                        $contexts = self::getContextsByNumber($key);
                        if (count($contexts) > 1) {

                            reset($contexts);
                            $contextID = key($contexts);
                            $contextName = $contexts[$contextID];

                            unset($assignedInPool[$key], $contexts[$contextID]);

                            $assignedInPool[$key .'_' .$contextID] = $number . '@' .$contextName;

                            foreach ($contexts as $contextID => $contextName) {
                                $assignedInPool[$key .'_' .$contextID] = $number . '@' .$contextName;
                            }
                        }
                    }
                }

                // for each of the assigned numbers in the pool
                foreach ($assignedInPool as $key => $number) {
                    $contexts = self::getContextsByNumber($key);

                    // if we have been asked to use names instead of numbers
                    if ($data['useNames'])
                    {
                        // get the destinations record
                        $destination = self::getDestinationByNumber($key);

                        // does it have a column called 'name'
                        if (!empty($destination['name'])) {
                            // if so then set a select option using the name
                            $assignedInPool[$key .'" class="' .$type] = $destination['name'] .' (' .$number .')';
                        } else {
                            // if not then set a select option using the number
                            $assignedInPool[$key .'" class="' .$type] = $number;
                        }
                    } else {
                        // set a select option using the number
                        $assignedInPool[$key .'" class="' .$type] = $number;
                    }

                    // because we hacked the our class into that for the jquery stuff
                    // we need to also modify the selected value to match
                    if ($selected == $key) {
                        $selected = $key .'" class="' .$type;
                    }

                    // remove the unmodified value from the array
                    unset($assignedInPool[$key]);
                }
                // This simple cache keeps us from redoing all these queries,
                // by my measure it saves 4 - 5 seconds on subsequent calls....
                self::$numbersCache[$type] = $assignedInPool;
            }

            if ($data['optGroups']) {
                // if we are are displaying as opgroups then add this as a sub-
                // array with the poolName as the key
                $options[$poolName] = $assignedInPool;
            } else {
                // if we are not using optGroups merge this into a single array
                $options = array_merge($options, $assignedInPool);
            }
        }
        unset($data['classType'], $data['optGroups'], $data['useNames'], $data['nullOption'], $data['contextAware']);

        return form::dropdown($data, $options, $selected, $extra);
    }

    /**
     * This will generate a list of all possible destinations of a pool or list
     * of pools.  The values of the select option will be the foreign id of the
     * destination.
     *
     * Additional Data Options:
     *  classType  = a single pool type to list or an array of types, if empty
     *                   all are used.
     *  optGroups  = Render the select options in optgroups by pool type
     *  nullOption = If this is a string then it is used as  the '0' option, or
     *                   if false then no such option will exist
     *  assigned   = If true the the list will only contain destination with
     *                   numbers assigned
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        option key that should be selected by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function destinationsDropdown($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'None',
            'classType' => FALSE,
            'optGroups' => TRUE,
            'assigned' => FALSE
        );

        // if types was just a string then make it an array
        if ( !empty($data['classType']) && !is_array($data['classType']))
        {
            $data['classType'] = array($data['classType']);
        }

        // append or insert the class
        arr::update($data, 'class', ' destination_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption'])) {
            $options = array(0 => __($data['nullOption']));
        } else {
            $options = array();
        }
        unset($data['nullOption']);

        // check if we should attempt to fill the selected
        $selected = self::_attemptRePopulate($data['name'], $selected);

        // foreach of the number types build a optgroup
        $types = self::getPoolTypes();
        foreach($types as $type) {
            // skip this we have a list of number types to include and not this
            if (is_array($data['classType']) && !in_array($type, $data['classType'])) continue;

            $destinations = self::getDestinationsByPool($type);

            // no destinations for this pool, then skip
            if (!$destinations) {
                continue;
            }

            // make a user friendly name for the pool name
            $poolName = str_replace('Number', '', $type);

            $optGroup = array();
            foreach ($destinations as $destination) {
                // replace the key with the destination identifier
                $key = $destination->identifier();
                // TODO: since we only deal in single column keys flatten this
                //       but in the future this could cause issues...
                $key = reset($key);

                if ($data['assigned']) {
                    $assigned  = self::getAssignedNumbers($type, $key);
                    if (empty($assigned)) continue;
                }

                // if we have been asked to use names instead of numbers
                if (!empty($destination['name']))
                {
                    // if so then set a select option using the name
                    $optGroup[$key .'" class="' .$type] = $destination['name'];
                } else {
                    $optGroup[$key .'" class="' .$type] = $poolName .' ' .$key;
                }

                // because we hacked the our class into that for the jquery stuff
                // we need to also modify the selected value to match
                if ($selected == $key) {
                    $selected = $key .'" class="' .$type;
                }
            }

            if (empty($optGroup)) continue;

            if ($data['optGroups']) {
                // if we are are displaying as opgroups then add this as a sub-
                // array with the poolName as the key
                $options[$poolName] = $optGroup;
            } else {
                // if we are not using optGroups merge this into a single array
                $options = array_merge($options, $optGroup);
            }
        }
        unset($data['classType']);
        unset($data['optGroups']);
        unset($data['assigned']);

        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function poolsDropdown($data, $selected = NULL, $extra = '') {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'Select',
            'classType' => FALSE
        );

        // if types was just a string then make it an array
        if ( !empty($data['classType']) && !is_array($data['classType']))
        {
            $data['classType'] = array($data['classType']);
        }

        // append or insert the class
        arr::update($data, 'class', ' pools_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption'])) {
            $options = array(0 => __($data['nullOption']));
        } else {
            $options = array();
        }
        unset($data['nullOption']);

        // check if we should attempt to fill the selected
        $selected = self::_attemptRePopulate($data['name'], $selected);

        // foreach of the number pools make an option
        $pools = self::getPoolTypes();
        foreach($pools as $pool) {
            // skip this we have a list of number types to include and not this
            if (is_array($data['classType']) && !in_array($type, $data['classType'])) continue;

            // get the vars out of the class ex: 'DeviceNumber' model
            $classVars = get_class_vars($pool);

            // see if that model has a var called description
            if (empty($classVars['description'])) {
                // if not then clean up the number pool name and use that
                $classVars['description'] = str_replace('Number', '', $pool);
            }

            // set a select option with the description or our cleaned up name
            $options[$pool .'" title="' .$pool] = $classVars['description'];
        }
        unset($data['classType']);

        // we are hacking the dropdown helper a bit so we need the selected var
        // to match up with our perceived option value
        if (!empty($selected)) {
            $selected = $selected.'" title="' .$selected;
        }

        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function nextAvaliableLink($bindTo, $title = NULL, $attributes = array(), $javascript = TRUE) {
        if (empty($bindTo)) return FALSE;

        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($attributes) )
        {
            $attributes = array('id' => $attributes);
        }

        // add in all the defaults if they are not provided
        $attributes += array(
            'id' => 'next' .$bindTo .'Link',
            'translate' => TRUE,
            'jgrowl' => TRUE
        );

        // ensure we have our distint class
        arr::update($attributes, 'class', ' nxt_aval_link');

        // if there is no title then use the default
        if (empty($title)) {
            $title = 'Next Avaliable Number';
        }

        // unless instructed otherwise translate this title
        if($attributes['translate']) {
            $title = __($title);
        }

        // if the user is not going to roll their own js do it for them
        if ($javascript) {

            // if the user wants to use jgrowl make sure it will be avaliable
            if ($attributes['jgrowl'])
                jquery::addPlugin('growl');

            // generate a js script to select the next avaliable number
            $script = '    $("#' .$attributes['id'] .'").bind("click", function(e) {
                e.preventDefault();
                numberDrop = $("#' .$bindTo .'");

                selected = [];
                $(numberDrop).find("option:selected").each( function() {
                    selected[selected.length] = $(this).text();
                });

                success = false;
                $(numberDrop).find("option").each(function () {
                    text = $(this).text();
                    if (jQuery.inArray(text, selected) == -1) {
                        $(this).attr("selected", "selected");
                        $(numberDrop).trigger("change");
                        ';

            // if the user wants to use jgrowl add it to our js script
            if ($attributes['jgrowl'])
                $script .= '$.jGrowl("' .__('Assigned number') .' " + text, { theme: "success", life: 5000 });
                        ';

            $script .= 'success = true;
                        return false;
                    }
                });
            ';

            // if the user wants to use jgrowl add it to our js script
            if ($attributes['jgrowl'])
                $script .= 'if (!success) $.jGrowl("' .__('Unable to find an avaliable number!') .'", { theme: "error", life: 5000 });';

            $script .= "\n" .'    });' ."\n";

            // put our script in the render stream
            javascript::codeBlock($script);
        }

        // dont inlcude the tranlaste in the html attributes
        unset($attributes['translate']);
        unset($attributes['jgrowl']);

        // Parsed URL
        return '<a href="' . url::current() .'" ' .html::attributes($attributes) .'><span>' .$title .'</span></a>';
    }

    public static function selectContext($data, $selected = NULL)
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // TODO: optimize this query, use DQL?
        $options = Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);

        if (!empty($data['nullOption'])) {
            $nullOption = array('context_id' => 0, 'name' => __($data['nullOption']));
            array_unshift($options, $nullOption);
            unset($data['nullOption']);
        }

        foreach ($options as $option) {
            $contextOptions[$option['context_id']] = $option['name'];
        }

        return form::dropdown($data, $contextOptions, $selected);
    }






    /**
     * Everything below this should be a library
     */

    /**
     * This will return an array of number pools with the keys as pool ID and
     * the values will be the pool name
     *
     * @return array
     */
    public static function getPoolTypes()
    {
        $pools = Doctrine_Query::create()
            ->select('nt.class')
            ->from('NumberType nt')
            ->distinct()
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $types = array();
        foreach($pools as $pool) {
            $types[$pool['number_type_id']] = $pool['class'];
        }

        return $types;
    }

    /**
     * This will return all numbers in a pool or list of pools.  The keys are
     * the number id and the values as the numbers
     *
     * @param mixed a string or array of pool names
     * @return array
     */
    public static function getNumbersByPool($numberType)
    {
        if (empty($numberType)) return array();

        if (!is_array($numberType)) $numberType = array($numberType);

        $numbers = Doctrine_Query::create()
            ->select('np.number_id, n.number')
            ->from('NumberPool np, np.Number n, np.NumberType nt')
            ->andwhereIn('nt.class', $numberType)
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $pool = array();
        foreach($numbers as $number) {
            $pool[$number['number_id']] = $number['Number']['number'];
        }

        return $pool;
    }

    /**
     * This will return all numbers in a pool or list of pools that are not
     * assigned.  The keys are the number id and the values as the numbers
     *
     * @param mixed a string or array of pool names
     * @return array
     */
    public static function getUnassignedNumbersByPool($numberType)
    {
        if (empty($numberType)) return array();

        if (!is_array($numberType)) $numberType = array($numberType);

        $numbers = Doctrine_Query::create()
            ->select('np.number_id, n.number')
            ->from('NumberPool np, np.Number n, np.NumberType nt')
            ->whereIn('n.foreign_id', array(0, 'NULL'))
            ->andwhereIn('nt.class', $numberType)
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $unassignedNumbers = array();
        foreach($numbers as $number) {
            $unassignedNumbers[$number['number_id']] = $number['Number']['number'];
        }

        return $unassignedNumbers;
    }

    /**
     * This will return all numbers in a pool or list of pools that are assigned.
     * The keys are the number id and the values as the numbers
     *
     * @param mixed a string or array of pool names
     * @return array
     */
    public static function getAssignedNumbersByPool($numberType)
    {
        if (empty($numberType)) return array();

        if (!is_array($numberType)) $numberType = array($numberType);

        $numbers = Doctrine_Query::create()
            ->select('np.number_id, n.number')
            ->from('NumberPool np, np.Number n, np.NumberType nt')
            ->whereNotIn('n.foreign_id', array(0, 'NULL'))
            ->andwhereIn('n.class_type', $numberType)
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $assignedNumbers = array();
        foreach($numbers as $number) {
            $assignedNumbers[$number['number_id']] = $number['Number']['number'];
        }

        return $assignedNumbers;
    }

    /**
     * This will return the pool name of an assigned number or
     * false if unassigned
     *
     * @param int the id of a number to check
     * @return mixed string or bool false
     */
    public static function getAssignedPoolByNumber($number_id)
    {
        if (empty($number_id)) return FALSE;

        $pool = Doctrine_Query::create()
            ->select('n.class_type')
            ->from('Number n')
            ->where("n.number_id = ?", array($number_id))
            ->andWhere('n.class_type IS NOT NULL')
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);

        return $pool;
    }

    /**
     * This function will return all doctine records of the destination class
     * based on the pool name. If the record can not be found or the number is
     * unassigned then it returns false.
     *
     * @param int the number id
     * @return mixeds a doctrine record object or bool false
     */
    public static function getDestinationsByPool($numberType) {
        // no number type? NO SOUP FOR YOU!
        if (empty($numberType)) return array();

        $class = str_replace('Number', '', $numberType);

        try {
            // get this classes table and identifier
            $table = Doctrine::getTable($class);

            // select all record in this table
            $records = $table->findAll();

            return $records;
        } catch (Exception $e) {
            // YOU ARE THE WEAKEST LINK, GOODBYE!
            kohana::log('error', 'Unable to get destinations by pool: ' .$e->getMessage());
            return FALSE;
        }
    }

    /**
     * This function will return a doctine record of the destination based on
     * the assigned numbers id. If the record can not be found or the number is
     * unassigned then it returns false.
     * 
     * @param int the number id
     * @return mixeds a doctrine record object or bool false
     */
    public static function getDestinationByNumber($number_id) {
        // no number id? NO SOUP FOR YOU!
        if (empty($number_id)) return FALSE;

        // get this numbers class_type and foreign_id
        $number = Doctrine_Query::create()
            ->select('n.class_type, n.foreign_id')
            ->from('Number n')
            ->where("n.number_id = ?", array($number_id))
            ->andWhere('n.class_type IS NOT NULL')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        // is the number not assigned?
        if (empty($number[0])) return FALSE;

        $class = str_replace('Number', '', $number[0]['class_type']);

        try {
            // get this classes table and identifier
            $table = Doctrine::getTable($class);
            $identifier = $table->getIdentifier();

            // select one record by the identifier and foreign_id
            $record = $table->findOneBy($identifier, $number[0]['foreign_id']);

            return $record;
        } catch (Exception $e) {
            // YOU ARE THE WEAKEST LINK, GOODBYE!
            kohana::log('error', 'Unable to get a numbers destination: ' .$e->getMessage());
            return FALSE;
        }
    }

    /**
     * This will return all numbers in a pool or list of pools that are assigned.
     * The keys are the number id and the values as the numbers
     *
     * @param mixed a string or array of pool names
     * @return mixed string or bool false
     */
    public static function getAssignedNumbers($class_type, $foreign_id)
    {
        // In a brand new record, foreign_id is blank/null (not even 0). So we shouldn't even be looking for numbers assigned
        if (empty($foreign_id)) return array();

        $numbers = Doctrine_Query::create()
            ->select('n.number_id, n.number')
            ->from('Number n')
            ->where('foreign_id = ?', array($foreign_id))
            ->andWhere('class_type = ?', $class_type)
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $assignedNumbers = array();
        foreach($numbers as $number) {
            $assignedNumbers[$number['number_id']] = $number['number'];
        }

        return $assignedNumbers;
    }

    /**
     * This will update the number assignment
     *
     * @param string the class type to assign the number to
     * @param int the foreign id of the class type to assign the number to
     * @param array the numbers to assign
     */
    public static function updateAssignment($class_type = NULL, $foreign_id = NULL, $numbers)
    {
        if(sizeof($numbers) == 0) {
        //its probably bad to treat NULLL like an array.  Need a cleaner method
            $numbers = array(); //prevents warning for $newNumbers = array_diff($numbers, $current);
        }

        // Cycle through the list of primary keys and update the relevant configuration files on disk based on the changes we just made
        if (Kohana::config('telephony.diskoutput')) {
        // Since we have to modify XML as well as update the DB, we just go record by record and make the relevant changes.
        // First, figure out what we have to do here. What's been changed?

            $query = Doctrine_Query::create()
                ->select('number_id')
                ->from('Number')
                ->where('foreign_id = ?', $foreign_id)
                ->andWhere('class_type = ?', $class_type);

            $results = $query->execute(array(), Doctrine::HYDRATE_ARRAY);

            $current = array();
            foreach ($results as $result) {
                $current[] = $result['number_id'];
            }

            // Process newly selected numbers
            $newNumbers = array_diff($numbers, $current);
            foreach ($newNumbers as $new) {
                Kohana::log('debug', 'Attempting to map number id # ' . $new . ' to ' . $class_type . ' id #' . $foreign_id);
                $number = Doctrine::getTable('Number')->find($new);
                $number->foreign_id = $foreign_id;
                $number->class_type = $class_type;
                $number->save();
            }

            // Process removed numbers
            $removedNumbers = array_diff($current, $numbers);
            foreach ($removedNumbers as $removed) {
                Kohana::log('debug', 'Attempting to unmap number id # ' . $removed . ' from ' . $class_type . ' id #' . $foreign_id);
                $number = Doctrine::getTable('Number')->find($removed);
                $number->foreign_id = 0;
                $number->class_type = NULL;
                $number->save();
            }
        } else {
        // Since writes to the XML file are not required, we can just update the DB quickly

            $q = Doctrine_Query::create()
                ->update('Number n')
                ->set('n.class_type', '?', '')
                ->set('n.foreign_id', '?', 0)
                ->where('n.foreign_id = ?', $foreign_id)
                ->andWhereIn('n.class_type', $class_type);

            $result = $q->execute();

            if(sizeof($numbers) != 0) //if the array is blank, sql grabs everything. not good. plus we have nothing to update.
            {
                $q = Doctrine_Query::create()
                    ->update('Number n')
                    ->set('n.class_type', '?', $class_type)
                    ->set('n.foreign_id', '?', $foreign_id )
                    ->whereIn('n.number_id', $numbers);
                $result = $q->execute();
            }
        }
    }

    public static function chooseDestination($inputField, $currentDestination = NULL, $allowedTypes = NULL)
    {
        jquery::addPlugin('dialog');

        
    }

    public static function getContextsByNumber($number_id) {
        if (empty($number_id)) return FALSE;


        $result = Doctrine_Query::create()
            ->select('n.number_id, nc.context_id, c.name')
            ->from('Number n, n.NumberContext nc, nc.Context c')
            ->where("n.number_id = ?", array($number_id))
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $contexts = array();
        foreach ($result as $number) {
            foreach ($number['NumberContext'] as $context) {
                $contexts[$context['Context']['context_id']] = $context['Context']['name'];                
            }
        }

        return $contexts;
    }
}
