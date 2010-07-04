<?php
class PackageVersionNotes extends Doctrine_Record 
{
    public function setTableDefinition()
    {
        $this->hasColumn('package_version_id', 'integer');
        $this->hasColumn('description', 'string', 255);
    }
    public function setUp()
    {
        $this->hasOne('PackageVersion', array(
            'local' => 'package_version_id', 'foreign' => 'id'
        ));
    }
}
