<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Bluebox_Relation extends Doctrine_Template
{
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

        $options = array('local' => $field, 'foreign' => $field);

        // Relate the plug-in to the base model (one to one)
        $this->_table->bind(array($this->baseModelName, $options), Doctrine_Relation::ONE);

        if ($this->getOption('cascade', $this->cascade))
        {
            $options['cascade'] = array('delete');
        }

        // Relate the base model to the plug-in (one/many to one)
        // Add relation to all extended models that may have already loaded
        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, $this->baseModelName) or ($class == $this->baseModelName))
            {
                $relateTable = Doctrine::getTable($class);
                
                $relateTable->bind(array($pluginModel, $options), $this->getOption('relationType', $this->relationType));
            }
        }
    }

    public function setTableDefinition()
    {
        $table = Doctrine::getTable($this->baseModelName);

        $field = $table->getIdentifier();

        if (!$this->_table->hasColumn($field))
        {
            $definition = $table->getColumnDefinition($field);

            $length = $definition['length'];
            
            $type = $definition['type'];

            $options = array_diff_key($definition, array_flip(array('type', 'length', 'autoincrement', 'primary')));

            $this->hasColumn($field, $type, $length, $options);
        }
    }
}
