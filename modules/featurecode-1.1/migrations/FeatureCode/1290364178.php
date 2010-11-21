<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCode_1 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('feature_code', 'account_id', 'integer', 11, array('unsigned' => true, 'default' => NULL));
    }

    public function down()
    {
        $this->removeColumn('feature_code', 'account_id');
    }
}