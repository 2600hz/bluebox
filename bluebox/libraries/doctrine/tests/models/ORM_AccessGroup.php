<?php
class ORM_AccessGroup extends Doctrine_Record 
{
    public function setTableDefinition() 
    {
        $this->hasColumn('name', 'string', 255);
    }
    public function setUp() 
    {
        $this->hasMany('ORM_AccessControl as accessControls', array(
            'local' => 'accessGroupID', 'foreign' => 'accessControlID', 'refClass' => 'ORM_AccessControlsGroups'
        ));
    }
}
