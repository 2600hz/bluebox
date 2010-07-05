<?php
/**
 * GenericStructure.php - GenericStructure behavior
 *
 * Support for storing variables in generically structured columns named registry and plugins
 * Allows you to arbitrarily make up field names and they will be serialized on save
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license Closed/Copyright Darren Schreiber
 */

class GenericStructure extends Doctrine_Template {

    public function setTableDefinition()
    {
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }
}
