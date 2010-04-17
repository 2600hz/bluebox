<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 *
 * TCAPI Telephony Configuration User Interface Application and Framework
 * Copyright (C) 2008, Darren Schreiber <d@d-man.org>
 *
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is TCAPI Telephony Configuration User Interface Application and Framework
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Darren Schreiber <d@d-man.org>
 *
 *
 * @author Darren Schreiber (pyite) <d@d-man.org>
 * @copyright (C) 2008, Darren Schreiber <d@d-man.org>
 * @license MPL 1.1
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 * @access public
 * @version 0.1
*/

class FreeSwitch extends Telephony_Driver
{
    /**
     * List of files that have already been loaded into memory (so we don't load them again)
     * @var array
     */
    protected $loaded = array();

    /**
     * DOMDocument of the current XML configuration, as it exists so far
     * @var DOMDocument
     */
    public $xml = NULL;

    // TODO: Move me! Belongs in FreeSWITCH driver land
    protected $sectionsUsed = array();

    private static $aclDirty = FALSE;

    private static $dirty = FALSE;

    private static $sofiaDirty = FALSE;

    private static $trunksDirty = FALSE;

    protected static $sectionPaths = array(
        'user' => 	'//document/section[@name="directory"]/domain[@name="%s"]/groups/group[@name="default"]/users/user[@id="%s"]',
        'ringgroup' => '//document/section[@name="directory"]/domain[@name="%s"]/groups/group[@name="%s"]',
        'location' => '//document/section[@name="locations"]',
        'directory' => '//document/section[@name="directory"]',
        'dialplan' => '//document/section[@name="dialplan"]/context[@name="%s"]/extension[@name="%s"][@continue="true"]/condition[@field="destination_number"][@expression="%s"]',
        'modules' => '//document/section[@name="configuration"]/configuration[@name="modules.conf"]/modules',
        'netlist' => '//document/section[@name="configuration"]/configuration[@name="acl.conf"]/network-lists/list[@name="net_list_%s"][@default="%s"]',
        'conferences' => '//document/section[@name="configuration"]/configuration[@name="conference.conf"]',
        'conference_profile' => '//document/section[@name="configuration"]/configuration[@name="conference.conf"]/profiles/profile[@name="conference_%s"]',
        'sofia' => '//document/section[@name="configuration"]/configuration[@name="sofia.conf"]/profiles/profile[@name="%s"]',
        'gateway' => '//document/section[@name="configuration"]/configuration[@name="sofia.conf"]/profiles/profile[@name="%s"]/gateways/gateway[@name="%s"]',
        'voicemail' => '//document/section[@name="configuration"]/configuration[@name="voicemail.conf"]/profiles/profile[@name="%s"]',
        'odbc' => '//document/section[@name="odbc"]',
    );

    public static function getInstance()
    {
        // This will keep this driver from loading before we know
        // if we can extend the domdocument (such as during install)
        if (!class_exists('DOMDocument'))
            return NULL;

        if (!isset(self::$instance)) {
            self::$instance = new self();

            Kohana::log('debug', 'New instance of FreeSwitch telephony driver created.');

            self::reset();
        }

        return self::$instance;
    }


    /*****************************************
     * Configuration file/section management *
     *****************************************/
    
    public function load($fileOptions)
    {
        self::$dirty = FALSE;
        if (!isset($fileOptions['filename'])) {
            return FALSE;
        }

        // Did we already load this file? If so, return
        /*if (array_search($fileOptions['filename'], $this->loaded))
            return TRUE;*/

        if (file_exists($fileOptions['filename'])) {
            //$this->loaded[] = $fileOptions['filename'];

            $savedRoot = $this->xml->getXmlRoot();
            $this->xml->setXmlRoot('');

            // Read from disk and prep the old config
            $oldConfig = new DOMDocument();
            $oldConfig->preserveWhiteSpace = false;	// don't mess with whitespace in the file
            $oldConfig->formatOutput = true;			// format the output in a "pretty" style, per PHP's built-in formatting
            try {
                $oldConfig->load($fileOptions['filename']);
            } catch (Exception $e) {
                Kohana::log('error', 'Unable to load XML from ' . $fileOptions['filename'] . '! Error: ' . $e->getMessage());
                return FALSE;
            }

            // Prep our in-memory document. Note that we create an empty placeholder for importing in case it doesn't already exist, but
            // the place holder does NOT include the last element in the query! Otherwise we will cause issues if there are attributes
            // on the core tag used in the query
            $query = substr($fileOptions['query'], 0, strrpos($fileOptions['query'], '/'));
            $this->xml->set($query);
            $xp = new DOMXPath($this->xml);
            $base = $xp->query($query);

            // Deal with the fact that root include tags are stripped out by FS, but not every file has an include! How annoying...
            if ($oldConfig->documentElement->tagName == 'include') {
                // Because there could be multiple items inside an <include>, but we want to ignore the include, we must import all childnodes
                $oldConfigRoot = $oldConfig->documentElement->childNodes;
                foreach ($oldConfigRoot as $oldConfigNode) {
                    $oldConfigNode = $this->xml->importNode($oldConfigNode, TRUE);
                    $base->item(0)->appendChild($oldConfigNode);
                }
            } else {
                // Import everything - including the root (we are sure there's only one, per XML spec)
                // NOTE: This will "fix" the jenky include issue listed above and ensure every file starts with an include in it's root
                $oldConfigRoot = $oldConfig->documentElement;
                $oldConfigNode = $this->xml->importNode($oldConfigRoot, TRUE);
                $base->item(0)->appendChild($oldConfigNode);
            }

            $this->xml->setXmlRoot($savedRoot);
        }

        // Process any includes in the content that was just loaded (recursively)
    }

    public function saveSection($fileOptions)
    {
        // Go load the requested XML info
        if ($fileOptions == NULL) {
            return;
        }

        if ($fileOptions['query'] == '//document') {
            // TODO: Save the base file
            return FALSE;
        }

        $configDoc = $this->xml;
        $xp = new DOMXPath($configDoc);

        // First, see if the section requested exists in the config
        $elements = $xp->query($fileOptions['query']);
        if (!is_null($elements)) {
            $file = $fileOptions['filename'];

            // Prep the destination doc
            $includeDoc = new DOMDocument();
            $includeDoc->preserveWhiteSpace = false;	// don't mess with whitespace in the file
            $includeDoc->formatOutput = true;			// format the output in a "pretty" style, per PHP's built-in formatting
            $includeRoot = $includeDoc->createElement('include');

            // Cycle through everything to save, and save it!
            if ($elements) {
                foreach ($elements as $element) {
                    /*if (isset($fileOptions['id'])) {
                        $ids = $xp->query($fileOptions['id'], $element);
                        if ($ids)
                            $file = $fileOptions['filename'] . $ids->item(0)->value . '.xml';
                    }*/

                    $includeRoot->appendChild($includeDoc->importNode($element, TRUE));

                    // Remove the elements we just grabbed from the master document and replace with an include
                    $parent = $element->parentNode;
                    $parent->removeChild($element);
                }

                // Now add an 'include' statement in place of the element we removed
                $includeNode = $configDoc->createElement('X-PRE-PROCESS');
                $includeNode->setAttribute('cmd', 'include');
                $includeNode->setAttribute('data', $file);
                if (isset($parent))
                {
                    $parent->appendChild($includeNode);
                } else {
                    $configDoc->appendChild($includeNode);
                }
            }
            
            $includeDoc->appendChild($includeRoot);

            // Save our include file here
            Kohana::log('debug', 'Saving config data to ' . $file);

            if((!file_exists($file)) or is_writable($file)) /* upgrades, system migration or just playing with chmod might result in not being able to write to the file */
            {
                $fp = fopen($file, 'w');
                fputs ($fp, $includeDoc->saveXML());
                fclose($fp);
            } else {
                Kohana::log('debug', 'File ' . $file . ' is not writable');
                throw new Exception(__("File $file is not writable.  Please correct this issue before saving."));
            }

            Kohana::log('debug', 'Done saving config file ' . $file);

        }

        // Replace the stripped out document with an include

        // TODO: Presume that if we save any files, we are going to need to reloadXML. This should be more intelligent at some point
        self::$dirty = TRUE;
    }

    public function save($sections = NULL)
    {
        if (!$sections) {
            $sections = array_keys($this->sectionsUsed);
        }

        $filemap = Kohana::config('freeswitch.filemap');

        // Cycle through everything that was presumed loaded/created and assume it needs to be written to disk/updated
        foreach ($sections as $key) if (isset($filemap[$key]['filename'])) {
            $fileOptions = $filemap[$key];
            Kohana::log('debug', 'Requesting save of section ' . $fileOptions['filename'] . '...');
            $this->saveSection($fileOptions);
            unset($this->sectionsUsed[$key]);
        }
    }

    /**
     * By default we are rendering our own object
     * @return string
     */
    public function render()
    {
        $this->xml->formatOutput = true;

        return $this->xml->saveXML();
    }

    public function renderPartial($xpath, $root = NULL)
    {
        // Seek the location with $this->xml that is specified by the xpath and render only things below it.
        // Optionally add a secondary root element
    }

    public function reset()
    {
        // Clear list of already loaded files
        // TODO: This could be MUCH more efficient. If it's already loaded, don't load it again!
        self::$instance->loaded = array();

        // The first time the FreeSwitch driver is instantiated we make this the root element. THIS IS IMPORTANT!
        // Even though we may never return elements this high in the DOMDocument, if you don't add this, you are
        // not allowing for various config sections to be daisy-chained into one big file. Remember, FS operates
        // on one big XML file in memory when it's using static files, so that should be considered the main concept here.
        // Use the "renderPartial" function if you only want a subsection of the HTML for, say, returning XML Curl
        //$this->xml = new SimpleXMLElement('<?xml version="1.0"? ><document type="freeswitch/xml"/>');
        $xml = new FsDomDocument();
        $xml->preserveWhiteSpace = false;	// don't mess with whitespace in the file
        $xml->formatOutput = true;			// format the output in a "pretty" style, per PHP's built-in formatting

        $baseElement = $xml->createElement('document');
        $baseElement->setAttribute('type', 'freeswitch/xml');
        $xml->appendChild($baseElement);

        self::$instance->xml = $xml;
    }

    public function commit()
    {
        // Activate any changed settings on the switch, live
        if (!FreePbx_Core::is_installing()) {
            if (class_exists('EslManager', TRUE) && (self::$dirty or self::$aclDirty or self::$sofiaDirty)) {
                $esl = new EslManager();
                if (self::$dirty) {
                    $esl->reloadxml();
                }

                if (self::$aclDirty) {
                    $esl->reloadacl();
                }

                // NOTE: Sofia reload must come after reloadxml, and also note that sofia reload implies trunk reload so this is an elseif
                if (self::$sofiaDirty) {
                    $esl->reload('mod_sofia');
                }
            }
        }
        self::$dirty = FALSE;
        self::$aclDirty = FALSE;
        self::$sofiaDirty = FALSE;
        self::$trunksDirty = FALSE;
    }


    /**********************************
     * Individual config file helpers *
     **********************************/
    // TODO: Find a better home for these? FsDomDocument maybe?

    /**
     * Get the XPath string related to a specific section of the config file
     * @param string $sectionName Shortname of the section
     * @return string XPath string that gets us to the section we want from the FreeSWITCH document's root element
     */
    public static function getSectionPath($sectionName)
    {
        $args = func_get_args();
        array_shift($args);
        $tmp = self::$sectionPaths[$sectionName];
        if ($args)
            $tmp = vsprintf($tmp, $args);

        return $tmp;
    }

    /**
     * Set the prefix for all upcoming set()/update() requests, based on a friendly section name (like 'dialplan' or 'directory')
     * Use this helper method to set the section the subsequent set()/update() requests should go to. This does two things:
     *   1) Prevents having to add a big long xpath to every set/update request
     *   2) Hides some of the uglyness in deeply nested XML elements within the FreeSWITCH config, so that we closer resemble
     *      the stock samples that come with FreeSWITCH so people are more comfortable using our helper methods
     *
     * Usage is:
     *   FreeSwitch::setSection('dialplan');
     *   FreeSwitch::getInstance()->xml->set('/mysetting');
     *
     * Note that sprintf is utilized to populate any additional variables you pass into the section XPath (if the xpath supports it).
     * So an alternate usage for users:
     *   FreeSwitch::setSection('users', 'mysystem.com', 'bob');
     *
     * Would allow for setting/getting variables for the domain 'mysystem.com' and user named bob in our users directory.
     *
     * @param string $sectionName Shortname of the section we want to work with
     * @return FsDomDocument Return the FsDomDocument element with the prefix now set for us (normally you ignore this returned var)
     */
    public static function setSection($sectionName)
    {
        $args = func_get_args();
        array_shift($args);
        if ($args) {
            self::$instance->xml->setXmlRoot(vsprintf(self::$sectionPaths[$sectionName], $args));
        } else {
            self::$instance->xml->setXmlRoot(self::$sectionPaths[$sectionName]);
        }

        if ($sectionName == 'netlist') {
            self::$aclDirty = TRUE;
        }

        if (($sectionName == 'sofia') or ($sectionName == 'gateway')) {
            self::$sofiaDirty = TRUE;
        }

        return self::$instance->xml; // This probably isn't necessary. Could just return nothing.
    }

    public static function createExtension($extensionName, $section = NULL, $context = NULL, array $options = array())
    {
        if (!$context) {
            if (self::getCurrentContext()) {
                $context = self::getCurrentContext();
            } else {
                // Context is required - fail here.
                return FALSE;
            }
        }

        if (!$section) {
            if (self::getCurrentSection()) {
                $section = self::getCurrentSection();
            } else {
                $section = 'main';  // For regular desinations, add main_ to them by default
            }
        }

        // Reference to our XML document & context
        self::$instance->xml->setXmlRoot('//document/section[@name="dialplan"]/context[@name="' . $context . '"]');

        // First, ensure our context exists, and create it if it doesn't
        self::$instance->xml->update('');

        self::$instance->xml->setXmlRoot('');

        // Get our own XPath stuff ready
        $xp = new DOMXPath(self::$instance->xml);

        // Get a reference to the root context node, for use wherever
        $elements = $xp->query('//document/section[@name="dialplan"]/context[@name="' . $context . '"]');
        $contextRootNode = $elements->item(0);

        // Next, see if the extension exists. If not, create it but respect the insert before/after parameters so things
        // happen in the right order.
        // NOTE: This will NOT fix people who have manually screwed up the order of their dialplan
        $elements = $xp->query('//document/section[@name="dialplan"]/context[@name="' . $context . '"]/extension[@name="' . $section . '_' . $extensionName . '"]');

        if ($elements->length == 0) {
            $sections = self::getDialplanSections();
            // This is a new extension we are creating - search for where to put it by finding the last item above it
            $insert_position = array_search($section, $sections);

            // Keep going up the document from the bottom until we find the right section
            while ($insert_position > 0 and ($elements->length == 0)) {
                $insert_position--;
                $elements = $xp->query('//document/section[@name="dialplan"]/context[@name="' . $context . '"]/extension[starts-with(@name,"' . $sections[$insert_position] . '_")]');
            }

            $newNode = self::$instance->xml->createElement('extension');
            $newNode->setAttribute('name', $section . '_' . $extensionName);
            $newNode->setAttribute('continue', 'true');
            // Did we fail at finding the right place to insert? If so, just insert at the top
            if ($insert_position == 0) {
                Kohana::log('debug', 'Adding ' . $section . '_' . $extensionName . ' at top of context ' . $context);
                $contextRootNode->insertBefore($newNode, $contextRootNode->firstChild);
            } else {
                // Otherwise, we found where to insert. Append away!

                // Get the last element from the search, we will insert immediately after it
                $element = $elements->item($elements->length - 1);
                Kohana::log('debug', 'Adding ' . $section . '_' . $extensionName . ' after ' . $element->nodeName . ' in context ' . $context);
                self::$instance->xml->appendSibling($newNode, $element);
            }
        } else {
            Kohana::log('debug', 'Extension ' . $section . '_' . $extensionName . ' already exists in context ' . $context . '. Not adding anything.');
        }

        // Set our XML Root automatically to be inside this extension, presuming the next commands will add/modify stuff to it
        self::$instance->xml->setXmlRoot('//document/section[@name="dialplan"]/context[@name="' . $context . '"]/extension[@name="' . $section . '_' . $extensionName . '"]');

        return self::$instance->xml;
    }

    /**
     * Register a section to make it available via a simple name to modules, instead of having long XPath strings all over the place.
     * Using the section helper methods here also prevents accidental disruption/deletion of other unrelated sections and loss of data.
     *
     * @param string $sectionName Shortname of the section being added
     * @param string $xpath XPath to the specific section in the config file
     */
    public static function registerSection($sectionName, $xpath)
    {
        // TODO: We should really validate the xpath before blindly adding it
        self::$sectionPaths[$sectionName] = $xpath;
    }

    // TODO: This method should be moved into the FreeSWITCH driver itself. It is not XML specific.
    public function autoloadXml($path)
    {
        // TODO: This method is called too frequently. It should only be called when something is actually missing from in-memory
        // otherwise we are wasting cycles and string comparisons
        $paths = (array)$path;

        foreach ($paths as $path) {
            foreach (Kohana::config('freeswitch.filemap') as $key => $options) if (isset($options['filename'])) {
                if (strpos($path, $options['query']) !== FALSE) {
                    // See if this is already in memory
                    if (!isset($this->sectionsUsed[$key])) {
                        $this->sectionsUsed[$key] = TRUE;
                        Kohana::log('debug', 'For query ' . $path . ' we\'re loading ' . $options['filename']);
                        Telephony::load($options);
                    }
                }
            }
        }
    }
}

/*
 *     function callCreate() {
		$this->output = <<<XML
<action application="ivr" data="1=extA;2=extB;3=extC"/>
XML;

    }

    function callTransfer() {

    }

    function callHold() {

    }

    function callProgress($current) {

    }

    function setMOH() {

    }

    function parkPush($lot = 'default') {
        $this->xml .= "";
    }

    function parkPop($destination) {

    }

    function get($variable, $value) {

    }

    function ifThenElse($condition, $action, $antiaction) {

    }

    function set($variable, $value) {

    }

    function queuePush() {

    }

    function queuePop() {

    }

    function queueDrop() {

    }


 */
