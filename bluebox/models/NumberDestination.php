<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * NumberDestination.php - NumberDestination class. Allows polymorphic relations between the numbers table and other
 * models on a 1:1 basis.
 *
 * This model allows a model to be the 'final destination' type for a phone number. In this way, it is easy to
 * execute dialplan routing and other hooks in order to have a phone number link to one, and only one, specific type
 * of object (a DID, a conference bridge, etc.) without having to create seperate tables or otherwise for each relation.
 * This concept helps avoid duplicate number entries and also creates a reservation type system for number management.
 *
 * Created on Jun 7, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class NumberDestination extends Doctrine_Record_Generator {
    protected $baseModelName = 'Number';

    protected $relationType = Doctrine_Relation::MANY;

    protected $cascade = FALSE;

    public function initOptions()
    {
        $this->setOption('className', '%CLASS%Number');
    }

    public function setUp()
    {
        // This class
        $class = get_class();

        // Get information about what we're plugging "into" (the base)
        $rootTable = Doctrine::getTable($this->baseModelName);  // Numbers model
        $field = $this->_table->getIdentifier();                   // primary key of current table

        // Get information about what's plugging into the base
        $pluginModel = $this->_table->getOption('name');        // Current model name

        $options = array('local'   => $field, 'foreign' => 'foreign_id');
        if ($this->cascade) {
            $options['onDelete'] = 'CASCADE';
        }

        // Relate the base model to the plug-in (one to one)
        //$rootTable->bind(array($pluginModel, array('local' => 'foreign_id', 'foreign' => $field)), $this->relationType);

        // Relate the plug-in to the base model (one to one)
        $this->_table->bind(array($this->baseModelName, $options), Doctrine_Relation::ONE);
    }

/*    public function setTableDefinition()
    {
        $rootTable = Doctrine::getTable($this->baseModelName);      // Numbers model
        
        // Get information about what's plugging into the base
        $pluginModel = $this->_table->getOption('name');        // Current model name

        if (!$this->_table->hasColumn($field)) {
            $this->hasColumn($field, 'integer', 11, array('unsigned' => true));
        }

        // Add this model as a subclass of the main model
        // Link all extended classes here, automagically
        //$this->SubClasses[$class] = array('foreign_type' => $class);
        // FIXME: Must add to what's already there!
        //$rootTable->setOption('subclasses', array($pluginModel => array('foreign_module_id' => $pluginModel)));
    }*/
}
