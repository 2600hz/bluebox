<?php
class Xmlcdr extends Bluebox_Record
{
	public function setTableDefinition()
    {
		$this->hasColumn('xml_cdr_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('caller_id_name', 'string', 50);
		$this->hasColumn('caller_id_number', 'string', 25);
		$this->hasColumn('direction', 'string', 15);
		$this->hasColumn('destination_number', 'string', 25);
		$this->hasColumn('user_name', 'string', 50);
		$this->hasColumn('context', 'string', 50);
		$this->hasColumn('start_stamp', 'timestamp');
		$this->hasColumn('answer_stamp', 'timestamp');
		$this->hasColumn('end_stamp', 'timestamp');
		$this->hasColumn('duration', 'integer');
		$this->hasColumn('billsec', 'integer' );
		$this->hasColumn('hangup_cause', 'string', 250);
		$this->hasColumn('uuid', 'string', 50);
		$this->hasColumn('bleg_uuid', 'string', 50);
		$this->hasColumn('accountcode', 'string', 25);
		$this->hasColumn('domain_name', 'string', 254);
		$this->hasColumn('user_context', 'string', 50);
		$this->hasColumn('read_codec', 'string', 10);
		$this->hasColumn('write_codec', 'string', 10);
		$this->hasColumn('dialed_domain', 'string', 254);
		$this->hasColumn('dialed_user', 'string', 50);
		$this->hasColumn('cdrrec', 'clob');
	}
}	