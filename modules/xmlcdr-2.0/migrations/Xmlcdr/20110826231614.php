<?php defined('SYSPATH') or die('No direct access allowed.');

class Migration_20110826231614 extends Doctrine_Migration_Base
{
    public function up()
    {
		$this->changeColumn('xmlcdr', 'caller_id_name', 50, 'string');
		$this->changeColumn('xmlcdr', 'caller_id_number', 25, 'string');
		$this->changeColumn('xmlcdr', 'direction', 15, 'string');
		$this->changeColumn('xmlcdr', 'destination_number', 25, 'string');
		$this->changeColumn('xmlcdr', 'user_name', 50, 'string');
		$this->changeColumn('xmlcdr', 'context', 50, 'string');
		$this->changeColumn('xmlcdr', 'start_stamp', null, 'timestamp');
		$this->changeColumn('xmlcdr', 'answer_stamp', null, 'timestamp');
		$this->changeColumn('xmlcdr', 'end_stamp', null, 'timestamp');
		$this->changeColumn('xmlcdr', 'hangup_cause',  250, 'string');
		$this->changeColumn('xmlcdr', 'uuid', 50, 'string');
		$this->changeColumn('xmlcdr', 'bleg_uuid', 50, 'string');
		$this->changeColumn('xmlcdr', 'accountcode', 25, 'string');
		$this->changeColumn('xmlcdr', 'domain_name', 254, 'string');
		$this->changeColumn('xmlcdr', 'user_context', 50, 'string');
		$this->changeColumn('xmlcdr', 'read_codec', 10, 'string');
		$this->changeColumn('xmlcdr', 'write_codec', 10, 'string');
		$this->changeColumn('xmlcdr', 'dialed_domain', 254, 'string');
		$this->changeColumn('xmlcdr', 'dialed_user', 50, 'string');
		$this->addColumn('xmlcdr', 'cdrrec', 'clob');
    }

    public function down()
    {
		$this->changeColumn('xmlcdr', 'caller_id_name', 1024, 'string');
		$this->changeColumn('xmlcdr', 'caller_id_number', 1024, 'string');
		$this->changeColumn('xmlcdr', 'direction', 1024, 'string');
		$this->changeColumn('xmlcdr', 'destination_number', 1024, 'string');
		$this->changeColumn('xmlcdr', 'user_name', 1024, 'string');
		$this->changeColumn('xmlcdr', 'context', 1024, 'string');
		$this->changeColumn('xmlcdr', 'start_stamp', 1024, 'string', 'string');
		$this->changeColumn('xmlcdr', 'answer_stamp', 1024, 'string', 'string');
		$this->changeColumn('xmlcdr', 'end_stamp', 1024, 'string', 'string');
		$this->changeColumn('xmlcdr', 'hangup_cause', 1024, 'string');
		$this->changeColumn('xmlcdr', 'uuid', 'string', 1024, 'string');
		$this->changeColumn('xmlcdr', 'bleg_uuid', 1024, 'string');
		$this->changeColumn('xmlcdr', 'accountcode', 1024, 'string');
		$this->changeColumn('xmlcdr', 'domain_name', 1024, 'string');
		$this->changeColumn('xmlcdr', 'user_context', 1024, 'string');
		$this->changeColumn('xmlcdr', 'read_codec', 1024, 'string');
		$this->changeColumn('xmlcdr', 'write_codec', 1024, 'string');
		$this->changeColumn('xmlcdr', 'dialed_domain', 1024, 'string');
		$this->changeColumn('xmlcdr', 'dialed_user', 1024, 'string');
		$this->removeColumn('xmlcdr', 'cdrrec');
    }
}

?>