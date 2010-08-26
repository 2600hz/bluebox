<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointPhonebook extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_phonebook_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('lastname', 'string', 30, array('notnull' => TRUE));
        $this->hasColumn('firstname', 'string', 30, array('notnull' => TRUE));
        $this->hasColumn('phonenumber', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('accountindex', 'integer', 2, array('default' => '0', 'notnull' => TRUE));
        $this->hasColumn('type', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('ext', 'integer', 4, array('default' => '0', 'notnull' => TRUE));

        //KEY `fname` (`LastName`,`FirstName`,`phonenumber`)
    }
}