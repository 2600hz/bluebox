<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * Relation.php - Allow for pluggable relations
 *
 * Abstract class that, when extended, allows for making an application "pluggable", and when a plugin uses this extended
 * class via $this->actAs() that class is related (both ways) 1:1 to the application model.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

abstract class FreePbx_Relation extends Doctrine_Template {
    protected $baseModelName;

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = FALSE;

    public function setUp()
    {
        // Get information about what we're plugging "into" (the base)
        $baseTable = Doctrine::getTable($this->baseModelName);
        $field = $baseTable->getIdentifier();

        // Get information about what's plugging into the base
        $pluginModel = $this->_table->getOption('name');

        $options = array('local'   => $field, 'foreign' => $field);
        if ($this->cascade) {
            $options['onDelete'] = 'CASCADE';
        }

        // Relate the plug-in to the base model (one to one)
        $this->_table->bind(array($this->baseModelName, $options), Doctrine_Relation::ONE);

        // Relate the base model to the plug-in (one/many to one)
        // Add relation to all extended models that may have already loaded
        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, $this->baseModelName) or ($class == $this->baseModelName)) {
                $relateTable = Doctrine::getTable($class);
                $relateTable->bind(array($pluginModel, array('local' => $field, 'foreign' => $field)), $this->relationType);
            }
        }
    }

    public function setTableDefinition()
    {
        $table = Doctrine::getTable($this->baseModelName);
        $field = $table->getIdentifier();

        if (!$this->_table->hasColumn($field)) {
            $this->hasColumn($field, 'integer', 11, array('unsigned' => true));
        }
    }
}
