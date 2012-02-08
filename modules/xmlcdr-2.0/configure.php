<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Michael Phillips & Rob Hutton
 * @license MPL
 * @package  xmlcdr
 *
 */
class Xmlcdr_2_0_Configure extends Bluebox_Configure
{
    public static $version = '2.0';
    public static $packageName = 'XML CDR';
    public static $author = 'Michael Phillips & Rob Hutton';
    public static $vendor = 'Michael Phillips & Rob Hutton';
    public static $license = 'MPL';
    public static $summary = 'Caller Detail Records via XML/CDR Records';
    public static $description = 'View caller records in a tablular format.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => '1.0.1'
    );
    public static $navLabel = 'CDR';
    public static $navBranch = '/Reports/';
    public static $navURL = 'xmlcdr/index';
    
    public function postInstall()
    {

        $settings = Doctrine::getTable('XmlcdrSetting')->findOneByFshost(Kohana::config('freeswitch.ESLHost'));
        if( ! $settings) {
            $settings = new XmlcdrSetting;
        }

        $registry = array(
            'url' => 'http://' . $_SERVER['HTTP_HOST'] . url::site('xmlcdr/service'),
            'log-dir' => '',
            'log-b-leg' => 'false',
            'prefix-a-leg' => 'true',
            'encode' => 'true',
            'disable-100-continue' => 'true'
        );

        $settings->fshost = Kohana::config('freeswitch.ESLHost');
        $settings->registry = $registry; 
        $settings->save();

    }

    public function repair()
    {
        self::postInstall();
    }
}
?>
