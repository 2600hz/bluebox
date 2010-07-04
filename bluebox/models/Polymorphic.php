<?php defined('SYSPATH') or die('No direct access allowed.');

class Polymorphic extends Doctrine_Template {
    const DEBUG = FALSE;
    
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
            if (self::DEBUG) kohana::log('debug', 'We are defining subclasses for ' . $thisClassName . '...');
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

        if (!empty($thisClass->relationType)) {
            $this->relationType = $thisClass->relationType;
            $mode = ' hasMany ';
        } else {
            $mode = ' hasOne ';
        }


        // If this class is extending Bluebox_Class directly, then load any possibly related models and setup our subclasses
        if (get_parent_class($thisClass) == 'Bluebox_Record') {
            // Go through every model and figure out if it is an extension of this model. If so, relate it via subclasses
            // Note that for efficiency, we assume that we only need to inspect classes that end in the same name as our base class
            // i.e. if the base class is named 'Device' we would only look at other models named things like 'SipDevice' or 'IaxDevice'
            //kohana::log('debug', '*** We are setting up the base class ' . $thisClassName . '...');
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
            
            if (self::DEBUG) kohana::log('debug', '*** Setting up extended version of ' . $baseClassName . ' (' . $thisClassName .")");
            if (class_exists($relatesTo, TRUE)) {
                $relatedPrimaryKey = Doctrine::getTable($relatesTo)->getIdentifier();

                if (self::DEBUG) kohana::log('debug', $thisClassName . $mode . $relatesTo);
                // Relate this class to the foreign model. So if this was DeviceNumber, this would relate us to Device
                if ($this->relationType == Doctrine_Relation::MANY) {
                    $thisClass->hasMany($relatesTo, array('local' => 'foreign_id', 'foreign' => $relatedPrimaryKey), $this->relationType);
                } else {
                    $thisClass->hasOne($relatesTo, array('local' => 'foreign_id', 'foreign' => $relatedPrimaryKey), $this->relationType);
                }
                
                // Also relate the base class to the foreign model.
                if (self::DEBUG) kohana::log('debug', $baseClassName . $mode . $relatesTo);
                $foreignTable = Doctrine::getTable($baseClassName);
                $foreignTable->bind(array($relatesTo, array('local' => 'foreign_id', 'foreign' => $relatedPrimaryKey)), $this->relationType);
                
                // Unfortunately, we can't turn off individual constraints in Doctrine. So we must turn off constratints for the
                // entire table, because foreign_id may map to multiple different other tables. This is only necessary on
                // concrete tables, since the inheritance tables aren't "real" tables and don't get created anyway.
                // TODO: Find a better solution to keeping constraint checks !
                $thisClass->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);
                $foreignTable->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

                // Create the relation on the other side, too
                if (self::DEBUG) kohana::log('debug', $relatesTo . $mode . $thisClassName . " (as $baseClassName)");
                $foreignTable = Doctrine::getTable($relatesTo);
                $foreignTable->bind(array($thisClassName . ' as ' . $baseClassName, array('local' => $relatedPrimaryKey, 'foreign' => 'foreign_id')), $this->relationType);

                // Now also relate to subclasses of the foreign model. So if there were other types of Devices, relate us to them, too
                foreach (Doctrine::getLoadedModelFiles() as $model => $dir) if (substr($model, strlen($model) - strlen($relatesTo), strlen($relatesTo)) == $relatesTo)
                {
                    if (is_subclass_of($model, $relatesTo)) {
                        if (self::DEBUG) kohana::log('debug', $model . $mode . $thisClassName . " (searched for instances of $relatesTo)\n");
                        Doctrine::getTable($thisClassName)->addRecordListener(new PolymorphicRecordListener($model));
                        $foreignTable = Doctrine::getTable($model);
                        $foreignTable->bind(array($thisClassName . ' as ' . $baseClassName, array('local' => $relatedPrimaryKey, 'foreign' => 'foreign_id')), $this->relationType);
                    }
                }
            }
        }
    }
}
