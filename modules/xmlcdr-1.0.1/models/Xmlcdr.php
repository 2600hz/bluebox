<?php
class Xmlcdr extends Bluebox_Record
{
	public function setTableDefinition()
    {
		$this->hasColumn('xml_cdr_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('caller_id_name', 'string', 1024);
		$this->hasColumn('caller_id_number', 'string', 1024);
                $this->hasColumn('direction', 'string', 1024);
                $this->hasColumn('destination_number', 'string', 1024);
                $this->hasColumn('user_name', 'string', 1024);
                $this->hasColumn('context', 'string', 1024);
                $this->hasColumn('start_stamp', 'string', 1024);
                $this->hasColumn('answer_stamp', 'string', 1024);
                $this->hasColumn('end_stamp', 'string', 1024);
                $this->hasColumn('duration', 'integer');
                $this->hasColumn('billsec', 'integer' );
                $this->hasColumn('hangup_cause', 'string', 1024);
                $this->hasColumn('uuid', 'string', 1024);
                $this->hasColumn('bleg_uuid', 'string', 1024);
                $this->hasColumn('accountcode', 'string', 1024);
                $this->hasColumn('domain_name', 'string', 1024);
                $this->hasColumn('user_context', 'string', 1024);
                $this->hasColumn('read_codec', 'string', 1024);
                $this->hasColumn('write_codec', 'string', 1024);
                $this->hasColumn('dialed_domain', 'string', 1024);
                $this->hasColumn('dialed_user', 'string', 1024);
	}
	
	public function setUp()
	{
		

	}
}	