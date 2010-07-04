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
 * Polymorphic.php - Polymorphic support class
 *
 * This behavior, when applied to a class that extends Bluebox_Record, results in an automatic setting of all Doctrine subclasses for
 * any class that extends the base class that applied this behavior.
 *
 * This behavior, when applied to a class that extends another model, results in an automatic attaching of the two named models in the
 * extended classes name. For example, if there is a base class named 'Number' and an extended class named 'DeviceNumber', an automatic
 * relation using column aggregation inheritance is established between the Device and Number models. Number is presumed to contain a
 * class_type and foreign_id field which track whether or not a record in the Number table is of type DeviceNumber and
 * inherently attached to devices.
 *
 * Created on Jul 25, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class Polymorphic extends Doctrine_Template {
    protected $relationType = Doctrine_Relation::ONE;

    public function setTableDefinition()
    {
        $thisClass = $this->getInvoker();
        $thisClassName = get_class($thisClass);
        
        if (!$this->_table->hasColumn('class_type')) {
            $this->hasColumn('class_type', 'string', 40);
        }

        if (!$this->_table->hasColumn('foreign_id')) {
            $this->hasColumn('foreign_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
        }
        
        // Find all classes which are extensions of this class and link them up, but only if this is a base polymorphic class
        if (get_parent_class($thisClass) == 'Bluebox_Record') {
            // Unfortunately, we can't turn off individual constraints in Doctrine. So we must turn off constratints for the
            // entire table, because foreign_id may map to multiple different other tables.
            // TODO: Find a better solution to keeping constraint checks intact!
            $thisClass->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

            $SubClasses = array();
            //echo 'We are defining subclasses for ' . $thisClassName . '...<BR>';
            foreach (Doctrine::getLoadedModelFiles() as $model => $dir) if (substr($model, strlen($model) - strlen($thisClassName), strlen($thisClassName)) == $thisClassName)
            {
                if (is_subclass_of($model, $thisClassName))
                {
                    // TODO: Lookup the ID number for this class
                    // echo "$class is a subclass of $thisClass. Linking...\n";
                    $SubClasses[$model] = array('class_type' => $model);
                }
            }

            // Link all extended classes here, automagically
            $thisClass->_table->setOption('subclasses', array_keys($SubClasses));
        } else {
            // Setup the inheritance map so that an extended class, when loaded or saved, maps all fields properly
            $thisClass->_table->setOption('inheritanceMap', array('class_type' => $thisClassName));
        }
    }

    public function setUp()
    {
        $thisClass = $this->getInvoker();
        $thisClassName = $thisClass->getTable()->getComponentName();

        // If this class is extending Bluebox_Class directly, then load any possibly related models and setup our subclasses
        if (get_parent_class($thisClass) == 'Bluebox_Record') {
            // Go through every model and figure out if it is an extension of this model. If so, relate it via subclasses
            // Note that for efficiency, we assume that we only need to inspect classes that end in the same name as our base class
            // i.e. if the base class is named 'Device' we would only look at other models named things like 'SipDevice' or 'IaxDevice'
            //echo '*** We are setting up the base class ' . $thisClassName . '...<BR>';
            foreach (Doctrine::getLoadedModelFiles() as $model => $dir) if (substr($model, strlen($model) - strlen($thisClassName), strlen($thisClassName)) == $thisClassName)
            {
                if (is_subclass_of($model, $thisClassName)) {
                    Doctrine::initializeModels($model);
                }
            }
        } else {
            // We are extending some other class. Figure out the pieces
            $baseClassName = get_parent_class($thisClass);
            $relatesTo = substr($thisClassName, 0, strlen($thisClassName) - strlen($baseClassName));
            
            //echo '*** We are setting up the extended version of ' . $baseClassName . ' (' . $thisClassName . ' hasOne => ' . $relatesTo . ")<BR>\n";
            if (class_exists($relatesTo, TRUE)) {
                $relatedPrimaryKey = Doctrine::getTable($relatesTo)->getIdentifier();

                // Relate this class to the foreign model. So if this was DeviceNumber, this would relate us to Device
                //echo $thisClassName . ' hasOne ' . $relatesTo . "<BR>\n";
                $thisClass->hasOne($relatesTo, array('local' => 'foreign_id', 'foreign' => $relatedPrimaryKey), $this->relationType);

                // Also relate the base class to the foreign model.
                //echo $baseClassName . ' hasOne ' . $relatesTo . "<BR>\n";
                $foreignTable = Doctrine::getTable($baseClassName);
                $foreignTable->bind(array($relatesTo, array('local' => 'foreign_id', 'foreign' => $relatedPrimaryKey)), $this->relationType);
                
                // Unfortunately, we can't turn off individual constraints in Doctrine. So we must turn off constratints for the
                // entire table, because foreign_id may map to multiple different other tables. This is only necessary on
                // concrete tables, since the inheritance tables aren't "real" tables and don't get created anyway.
                // TODO: Find a better solution to keeping constraint checks !
                $thisClass->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);
                $foreignTable->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

                // Create the relation on the other side, too
                //echo $relatesTo . ' hasOne ' . $thisClassName . " (as $baseClassName)<BR>\n";
                $foreignTable = Doctrine::getTable($relatesTo);
                $foreignTable->bind(array($thisClassName . ' as ' . $baseClassName, array('local' => $relatedPrimaryKey, 'foreign' => 'foreign_id')), $this->relationType);

                // Now also relate to subclasses of the foreign model. So if there were other types of Devices, relate us to them, too
                foreach (Doctrine::getLoadedModelFiles() as $model => $dir) if (substr($model, strlen($model) - strlen($relatesTo), strlen($relatesTo)) == $relatesTo)
                {
                    if (is_subclass_of($model, $relatesTo)) {
                        //echo $model . ' hasOne ' . $thisClassName . " (searched for instances of $relatesTo)<BR>\n";
                        Doctrine::getTable($thisClassName)->addRecordListener(new PolymorphicRecordListener($model));
                        $foreignTable = Doctrine::getTable($model);
                        $foreignTable->bind(array($thisClassName . ' as ' . $baseClassName, array('local' => $relatedPrimaryKey, 'foreign' => 'foreign_id')), $this->relationType);
                    }
                }
            }
        }
    }
}
