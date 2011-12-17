<?php defined('SYSPATH') or die('No direct access allowed.');

class Migration_1290364178 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('media_file', 'account_id', 'integer', 11, array('unsigned' => true, 'default' => NULL));
    }

    public function down()
    {
        $this->removeColumn('media_file', 'account_id');
    }
}
