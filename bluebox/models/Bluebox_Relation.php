<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Bluebox_Relation extends Doctrine_Template {
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
