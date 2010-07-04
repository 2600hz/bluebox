<?php
class VoicemailMessage extends Bluebox_Record
{
	public function setTableDefinition()
	{
		
		$this->setTableName('voicemail_msgs');
		$this->hasColumn('created_epoch', 'integer');
		$this->hasColumn('read_epoch', 'integer');
		$this->hasColumn('username', 'string', 255);
		$this->hasColumn('domain', 'string', 255);		
		$this->hasColumn('uuid', 'string', 255, array('primary' => true));
		$this->hasColumn('cid_name', 'string', 255);
		$this->hasColumn('cid_number', 'string', 255);
		$this->hasColumn('in_folder', 'string', 255);
		$this->hasColumn('file_path', 'string', 255);
		$this->hasColumn('message_len', 'int', 255);
		$this->hasColumn('flags', 'string', 255);
		$this->hasColumn('read_flags', 'string', 255);
		//$this->hasColumn('voicemail_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		
	}
	
	public function setUp()
	{
		
	}
}
