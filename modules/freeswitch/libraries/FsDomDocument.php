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

//define('XPATH_DEBUG', true);

class FsDomDocument extends DOMDocument
{
    /**
     * XPath to the root of FreeSwitch's config for the stuff we're working with in this module.
     * This variable is used by the driver as well as our FS support below - it's important if you wish to support
     * partial files.
     * @var string
     */
    protected $xmlRoot = NULL;
    protected $xmlExtenRoot = NULL;
    
    /**
     * Get the currently set prefix
     * @return <type>
     */
    public function getXmlRoot()
    {
        return $this->xmlRoot;
    }
    public function getExtensionRoot() {
        return $this->xmlExtenRoot;
    }

    public function setExtensionRoot($xmlExtenRoot) {
        $this->xmlExtenRoot = $xmlExtenRoot;
    }
    /**
     * Set the prefix to be appended to all set() and update() XPath calls
     * @param string $prefix XPath prefix to append before paths passed to set() and update(). Set to NULL to clear.
     * @return <type>
     */
    public function setXmlRoot($xmlRoot)
    {
        Kohana::log('debug', 'Setting XML root to ' . $xmlRoot);
        $this->xmlRoot = $xmlRoot;
    }

    public function preUpdate($xpaths)
    {
        // Tack on the XmlRoot to all xpaths
        if (is_string($xpaths)) {
            $xpaths = $this->xmlRoot . $xpaths;
        } elseif (is_array($xpaths)) {
            foreach ($xpaths as $k => $xpath) {
                $xpaths[$k] = $this->xmlRoot . $xpath;
            }
        }

        // TODO: Add validation here. This would help everyone out and allow for unified errors to be shown


        // TODO: Make this into a hook that drivers can attach to.
        Telephony::getDriver()->autoloadXml($xpaths);

        // Return the modified XPaths, completed
        return $xpaths;
    }

    /**
     * This method simplifies the creation of elements that have
     * multiple attributes. Passing in a DOMDocument element,
     * an array of key/value pairs, and an array of which keys you
     * actually want added to the element results in an element
     * with the appropriate attributes.
     *
     * @param DOMElement {$element} The DOM element to append attributes to
     * @param array {$data} An array of key/value pairs that you *might* want to add as attributes
     * @param array {$keys} An array of strings which are the keys you *actually* want added, and the keyname to be written. This is a mapping!
     *
     * @todo This is a placeholder function. It needs to be written.
     *
     */
    public function arrayToAttr(&$element, $data, $keymap)
    {
        foreach ($keymap as $newkey => $origkey) {
        // Was a key mapping given in the array? If origkey is an integer, assume not
            if (is_int ($newkey)) {
                // We get here if both the name of the new key and the old key are identical
                if (isset ($data[$origkey]))
                    $element->setAttribute ( $origkey, $data[$origkey] );
            } else {
                // We get here if there is some mapping of keys from old array to new element to do
                if (isset ($data[$origkey]))
                    $element->setAttribute( $newkey, $data[$origkey] );
            }
        }
    }

    /**
     * Get an attribute's value quickly using XPath string. Returns blank if no value.
     *
     * @param string XPath to the XML attribute which you want to retrieve
     * @param string Attribute name to retrieve (default is 'value')
     * @return string Value of attribute
     *
     */
    public function getAttributeValue($xpath, $attributename = 'value')
    {
        $xpath = $this->preUpdate($xpath);

        $xp = new DOMXPath($this);

        if (defined('XPATH_DEBUG')) {
            Kohana::log('debug', "Search Query Is: $xpath");
        }
        $elements = $xp->query($xpath);
        if ($elements->length > 0) {
            Kohana::log('debug', 'returning attribute');
            return $elements->item(0)->getAttribute($attributename);
        } else {
            Kohana::log('debug', 'returning false');
            return false;
        }
    }

    /**
     * Set an attribute's value quickly using XPath string. Returns blank if no value.
     *
     * @param string XPath to the XML attribute which you want to retrieve
     * @param string Attribute name to set/update (default is 'value')
     * @param string Value to set
     * @return string Value of attribute
     *
     */
    public function setAttributeValue($xpath, $attributename = 'value', $value)
    {
        $xpath = $this->preUpdate($xpath);

        $xp = new DOMXPath($this);
        if (defined('XPATH_DEBUG')) {
            Kohana::log('debug', "Search Query Is: $xpath");
        }

        $elements = $xp->query($xpath);
        if ($elements->length > 0) {
            Kohana::log('debug', 'setting attribute');
            return $elements->item(0)->setAttribute($attributename, $value);
        } else {
            Kohana::log('debug', 'returning false');
            return false;
        }
    }

    /**
     * Helper method for set(). Sets attributes quickly and easily using preg callbacks.
     */
    public function _setAttr($variables)
    {
        $variables[1] = str_replace ('"', '', $variables[1]);
        $variables[2] = str_replace ('"', '', $variables[2]);
        if ($variables[1]) {
            $this->newAttrs[$variables[1]] = $variables[2];
        }

        return '';
    }

    /**
     * This method takes a single DOMXPath query string and createss the relevant empty elements in the current
     * document if they do not already exist.
     *
     * This function always assumes you are working with paths that start at the document's root (//)
     *
     * NOTE: This function will also create attributes for any missing elements in a path
     *
     * @param mixed A string containing a path, or an array of multiple pahts to create
     * @return mixed Returns false if failed, otherwise returns the last-most node that was traversed (end of path)
     *
     */
    public function set($xpath, $args = NULL)
    {
        $xpath = $this->preUpdate($xpath);

        // Take slashes out temporarily, replace with placeholder
        $xpath = str_replace('\/', '__SLASH__', $xpath);

        // Break path into parts (divided by slahses)
        $elements = explode("/",$xpath);

        // Create XPath search object
        $xp = new DOMXPath($this);

        // Set our XML iterator at the base of this DOMDocument
        $currentnode = $this;

        // Traverse $path, adding elements that don't exist
        foreach ($elements as $element) if ($element != "") {
            // Put slashes back
            $element = str_replace('__SLASH__', '/', $element);

            $this->newAttrs = array();
            $elements = $xp->query($element, $currentnode);
            // Does path not exist (0 elements)?
            if ($elements->length == 0) {
                // Add any attributes in XPath to element
                // I checked for non quoted params, and didnt find any...
                // grep -R -P '\[@(.*?)=[^"](.*?)[^"]\]' ./* | grep -v .svn
                $elementname = preg_replace_callback ('/\[@(.*?)="(.*?)"\]/', array (&$this, "_setAttr"), $element);
                $new_element = $this->createElement($elementname);
                foreach ($this->newAttrs as $key => $value) {
                    $new_element->setAttribute($key, $value);
                }

                $currentnode->appendChild($new_element);

                $currentnode = $new_element;
            } else {
                // Path already exists so far, traverse existing path
                // NOTE: We assume only one possible entry is returned. This may be a mistake...?
                $currentnode = $elements->item(0);
            }
        }

        // Return the last-most node we created (assuming someone is about to add to that node)
        return $currentnode;
    }


    /**
     * An enhanced set() that allows for updating fields which may not have the same number of attributes on searching as on updating/replacing
     * This method supports the following scenarios:
     *  - Base XML element exists but has no or some of the required attributes for updating, not all (we add attributes in this case)
     *  - XML element exists with all required attributes (a straight replace)
     *  - XML element does not exist and needs to be created, along with all attributes
     *
     * The format is the same as XPath, except that fields which are considered optional during searching (but used in set/updating)
     * are wrapped in curly braces.
     *
     * As an example, if you wanted to add a voicemail box to a user's ID and the user may already exist, you would use:
     *   /document/section[@name="directory"]/domain/users/user[@id="device_A"]{@mailbox="1234"}
     * instead of:
     *   /document/section[@name="directory"]/domain/users/user[@id="device_A"][@mailbox="1234"]
     *
     * Note the curly braces in the first example. This allows for the mailbox attribute to not exist in the initial search, or to
     * exist and be updated. It is essentially omitted from the search but used in the update/replace of the found (or created) base
     * element.
     *
     * @param string $query XPath to search for and create if it doesn't exist. Can contain replace/update fields as well.
     */
    public function update($query)
    {
        // Replace any attribute fields containing %s or other sprintf modifiers, and hang on to the attribute name for use later
        // NOTE: We only allow one attribute at this point in time.
        preg_match_all ("/\{@(.*?)=\"(.*?)\"\}/", $query, $results);
        // Take all matches (if any) and put them in hash key/value form
        if (is_array($results)) {
            $newAttributes = array();
            for ($i = 0; $i < count($results[0]); $i++) {
                $newAttributes[$results[1][$i]] = $results[2][$i];
            }
        } else {
            $newAttributes = NULL;
        }
        
        // Create a search string (minus all update/replace attributes) for use in seeing if anything already exists
        $search = preg_replace ('/\{@(.*?)=\"(.*?)\"\}/', '', $query);

        // Prep the query string and load anything relevant into memory
        $search = $this->preUpdate($search);

        // Now, run the query and save the results
        if (defined('XPATH_DEBUG')) {
            Kohana::log('debug', "Searching for $search...");
        }

        $xpath = new DOMXPath ($this);
        if (defined('XPATH_DEBUG')) {
            Kohana::log('debug', "search query is: $search");
        }

        // Do the search
        $elements = $xpath->query($search);

        // Now figure out where we're storing the results, and store them accordingly

        // DOES THE BASE ELEMENT EXIST?
        if ($elements->length > 0) {
            if (defined('XPATH_DEBUG')) {
                Kohana::log('debug', "We found an exact match for $search! Easy replace for attributes...");
            }
            // Is this an attribute? If so, delete any like-named attribute and replace with new value
            foreach ($newAttributes as $name => $value) {
                if (defined('XPATH_DEBUG')) {
                    Kohana::log('debug', "Adding/updating attribute on this element.\n");
                }
                $value = str_replace('\/', '/', $value);
                if ($value == '') {
                // How do we delete attributes? We need to fix this...
                    $elements->item(0)->setAttribute($name, $value);
                } else {
                    $elements->item(0)->setAttribute($name, $value);
                }
            //                        $elements->item(0)->nodeValue = $formData[$fieldName];
            //                        echo "Replaced element value.\n";
            }
            if (defined('XPATH_DEBUG')) {
                Kohana::log('debug', "Replace success.");
            }
        } else {
        // NO BASE ELEMENT EXISTS
            if (defined('XPATH_DEBUG')) {
                Kohana::log('debug', "We did not find $query. Creating it instead.");
            }
            $create = preg_replace ('/\{@(.*?)=\"(.*?)\"\}/', '[@$1="$2"]', $query);
            $this->set($create);
        }

    }


    /**
     * This method takes a single DOMXPath query string and createss the relevant empty elements in the current
     * document if they do not already exist.
     *
     * This function always assumes you are working with paths that start at the document's root (//)
     *
     * NOTE: This function will NOT create attributes for any missing elements in a path
     *
     * @param mixed A string containing a path, or an array of multiple pahts to create
     * @return mixed Returns false if failed, otherwise returns the last-most node that was traversed (end of path)
     *
     */
    public function createPath($paths)
    {
        $paths = $this->preUpdate($paths);

        foreach ((array) $paths as $path) {
        // Break path into parts (divided by slahses)
            $elements = explode("/",$path);

            // Create XPath search object
            $xp = new DOMXPath($this);

            // Set our XML itterator at the base of this DOMDocument
            $currentnode = $this;

            // Traverse $path, adding elements that don't exist
            foreach ($elements as $element) if ($element != "") {
                    $entries = $xp->query($element, $currentnode);
                    // Does path not exist (0 elements)?
                    if ($entries->length == 0) {
                    // Remove attributes from XPath in element
                        $elementname = preg_replace ('/\[@.*?\]/', '', $element);
                        $new_element = $this->createElement($elementname);
                        $currentnode->appendChild($new_element);

                        $currentnode = $new_element;
                    } else {
                    // Path already exists so far, traverse existing path
                    // NOTE: We assume only one possible entry is returned. This may be a mistake...?
                        $currentnode = $entries->item(0);
                    }
                }
        }

        // Return the last-most node we created (assuming someone is about to add to that node)
        return $currentnode;
    }


    /**
     * This method takes DOMXPath style query strings and creates the relevant empty elements in the current
     * document if they do not already exist.
     *
     * NOTE: This function will NOT create attributes for any missing elements in a path, which can cause issues
     *
     * @param mixed A string containing a path, or an array of multiple pahts to create
     * @return boolean True if successful, false if otherwise
     *
     */
    public function createStructure($paths)
    {
        $success = true;

        if (is_array($paths)) {
            foreach ($paths as $path)
            // Were we successful in creating the path? If not, flag it
                if (!$this->createPath ($path))
                    $success = false;
        } else
            $success = $this->createPath ($paths);

        return $success;
    }

    public function replaceWithXml($query = '', $xml)
    {
        // Create the base if it doesn't already exist. Delete all children of the base
        $query = $this->preUpdate($query);
        
        $this->set($query);
        $this->deleteChildren($query);

        // Grab an XPath pointer to the query we just ran
        $xp = new DOMXPath($this->xml);
        $base = $xp->query($query);

        // Create a new XML fragment and append it to wherever $query pointed to
        $newXmlFragment = $doc->createDocumentFragment();
        $newXmlFragment->appendXML($xml);
        $base->documentElement->appendChild($newXmlFragment);
    }

    public function deleteNode($query = '')
    {
        $query = $this->preUpdate($query);

        // Create XPath search object
        $xp = new DOMXPath($this);
        $elements = $xp->query($query);

        foreach ($elements as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    public function deleteChildren($query = '')
    {
        $query = $this->preUpdate($query);
        
        // Create XPath search object
        $xp = new DOMXPath($this);
        $nodeList = $xp->query($query);

        foreach ($nodeList as $node) {
            while (isset($node->firstChild)) {
                $node->removeChild($node->firstChild);
            }
        }
    }

    /**
     * @param DOMNode $newnode Node to insert next to $ref
     * @param DOMNode $ref Reference node
     * @requires $ref has a parent node
     * @return DOMNode the real node inserted
     */
    function appendSibling(DOMNode $newnode, DOMNode $ref)
    {
        if ($ref->nextSibling) {
        // $ref has an immediate brother : insert newnode before this one
            return $ref->parentNode->insertBefore($newnode, $ref->nextSibling);
        } else {
        // $ref has no brother next to him : insert newnode as last child of his parent
            return $ref->parentNode->appendChild($newnode);
        }
    }

}
