 <?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_settings extends Bluebox_Record
{
    function setTableDefinition()
    {
        $this->hasColumn('cc_odbc_dsn', 'string', 50);
        $this->hasColumn('cc_db_name', 'string');
        $this->hasColumn('cc_update_mode', 'string', 10, array('default' => 'realtime'));
    }

    public function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}

?>