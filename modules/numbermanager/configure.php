<?php defined('SYSPATH') or die('No direct access allowed.');
class NumberManagement_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'numbermanager';
    public static $displayName = 'Number Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Number Management Controller Class';
    public static $description = 'When this module is loaded along with the above listed modules (and possibly others), address fields will automatically appear within relevant modules. In addition, an order of precedence is set for loaded modules as well. This plugin might be overridden by an alternative address module that has better support for international addresses, validation and other features. Make sure you don\'t have conflicts.';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Routing/';
    public static $navURL = 'numbermanager/index';    
    public static $navSubmenu = array(
        'Search Numbers' => '/numbermanager/index',
        'Add a Number' => '/numbermanager/add/dummy',
        'Add Multiple Numbers' => array(
            'url' => '/numbermanager/bulkadd/dummy'
        ) ,
        'Import Numbers' => array(
            'url' => '/numbermanager/import',
            'disabled' => true
        ) ,
        'Edit a Number' => array(
            'url' => '/numbermanager/edit',
            'disabled' => true
        ) ,
        'Delete Number' => array(
            'url' => '/numbermanager/delete',
            'disabled' => true
        )
    );
    public function completedInstall() {
        // Check if the user wants us to install sample numbers if we complete our install
        $installSamples = Session::instance()->get('installer.samples', FALSE);
        if (empty($installSamples)) return TRUE;

        // get a list of contexts we should attach too
        $contexts = Doctrine_Query::create()
            ->select('c.context_id')
            ->from('Context c')
            ->execute(array() , Doctrine::HYDRATE_ARRAY);

        Kohana::log('debug', 'Here 1 - NumberManagement');

        // create a set of defaults for our numbers
        $baseNumber = array (
            'location_id' => users::$user->location_id,
            'status' => 0,
            'class_type' => NULL,
            'foreign_id' => 0,
            'NumberContext' => $contexts
        );
        
        // Loop each number type
        $numberTypes = Doctrine::getTable('NumberType')->findAll();
        foreach ($numberTypes as $numberType) {
            // if this is a known number type then lets gens some numbers!
            switch ($numberType['class']) {
                case 'DeviceNumber':
                    $numberStart = 2010;
                    $numberEnd = 2020;
                    break;
                case 'ConferenceNumber':
                    $numberStart = 3000;
                    $numberEnd = 3005;
                    break;
                case 'RingGroupNumber':
                    $numberStart = 4000;
                    $numberEnd = 4010;
                    break;
                case 'AutoAttendantNumber':
                    $numberStart = 8000;
                    $numberEnd = 8003;
                    break;
                case 'SystemNumber':
                    $numberStart = 9900;
                    $numberEnd = 9910;
                    break;
                default:
                    continue 2;
                    break;
            }

            kohana::log('debug', 'Installing sample numbers of type ' . $numberType['class']);

            // set our numbertype to match the current
            $numberPool = array('number_type_id' => $numberType['number_type_id']);
            $baseNumber['NumberPool'] = array($numberPool);

            // gen some number for this type
            $numbers = new Doctrine_Collection('Number');
            for ($numberIterator = $numberStart; $numberIterator <= $numberEnd; $numberIterator++) {
                $number = new Number();
                $number->number = number_format($numberIterator, 0, '.', '');
                $number->synchronizeWithArray($baseNumber);
                $numbers[] = $number;
            }
            try {
                $numbers->save();
                $numbers->free(TRUE);
            } catch (Exception $e) {
                // We suppress errors because the most likely is an existing number....
                kohana::log('error', 'Unable to add sample numbers! ' . $e->getMessage());
                return array('warnings' => array('Unable to add sample numbers! ' . $e->getMessage()));
            }
        }
    }
}