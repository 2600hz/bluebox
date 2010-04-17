<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * DeviceManager_Configure - Configuration metadata / class
 *
 * Gives info about how to install this module
 *
 * @author Darren Schreiber
 * @package FreePBX3
 * @subpackage Device_Manager
 */
class DeviceManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'devicemanager';
    public static $displayName = 'Device Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Provides Device Management';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navLabel = 'Devices';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'devicemanager/index';
    public static $navSubmenu = array(
        'Search Devices' => 'devicemanager/index',
        'Add Device' => 'devicemanager/add',
        'Edit Device' => array(
            'url' => 'devicemanager/edit',
            'disabled' => true
        ) ,
        'Delete Device' => array(
            'url' => 'devicemanager/delete',
            'disabled' => true
        )
    );

    public function newTenant() {

    }

    public function completedInstall() {
        // Check if the user wants us to install sample numbers if we complete our install
        $installSamples = Session::instance()->get('installer.samples', FALSE);
        if (empty($installSamples)) return TRUE;

        $contexts = Doctrine_Query::create()
            ->select('c.context_id')
            ->from('Context c')
            ->execute(array() , Doctrine::HYDRATE_ARRAY);

        $numberType = Doctrine_Query::create()
            ->select('n.number_type_id')
            ->from('NumberType n')
            ->where('n.class = ?', 'DeviceNumber')
            ->execute(array() , Doctrine::HYDRATE_ARRAY);

        /*$user = Doctrine::getTable('User')->findAll();
        $accountId = $user[0]->Location->account_id;
        $user = $user[0]->toArray();*/

        $user = users::$user;
        $accountId = $user->account_id;


        if (empty($numberType) || empty($user)) {
            kohana::log('error', 'NumberType or User empty while generating sample devices');
            return array('warnings' => array('Unable to generate sample devices!'));
        }

        $baseDevice = array(
            'user_id' => $user['user_id'],
            'name' => 'Device 1',
            'context_id' => NULL,
            'class_type' => 'SipDevice',
            'Sip' => array (
                'username' => '2000',
                'password' => '2000a',
                'cid_format' => 0,
                'sip_invite_format' => 0,
                'from_user' => NULL,
                'from_domain' => NULL,
                'mac_address' => NULL
            ),
            'Number' => array (
                'location_id' => NULL,
                'status' => 0,
                'class_type' => 'DeviceNumber',
                'NumberContext' => $contexts,
                'NumberPool' => $numberType
            )
        );
        $voicemailBox = array (
                'name' => '2000',
                'mailbox' => '2000',
                'password' => '1234',
                'email_address' => NULL,
                'delete_file' => 0,
                'audio_format' => NULL,
                'email_all_messages' => 0,
                'enabled' => 1,
                'account_id' => $accountId
        );

        $devices = new Doctrine_Collection('Device');
        $loopIterator = 1;
        for ($deviceIterator = 2000; $deviceIterator <= 2009; $deviceIterator++) {
                $baseDevice['name'] = 'Device ' . $loopIterator;
                $baseDevice['Sip']['username'] = 'sip'. $loopIterator;
                $baseDevice['Sip']['password'] = $this->generatePassword() .'a';
                $baseDevice['Sip']['contact'] = '';
                $baseDevice['Number']['number'] = $deviceIterator;

                $newDevice = new Device;
                $newDevice->synchronizeWithArray($baseDevice);

                // Check to see if VoicemailSettings exists. If so enable voicemail on sample devices.
                $voicemailEnabled = Session::instance()->get('installer.install_voicemail', FALSE);
                if( ! empty($voicemailEnabled)) {
                    $voicemailBox['name'] = 'Box ' .$deviceIterator;
                    $voicemailBox['mailbox'] = $deviceIterator;
                    $voicemailBox['password'] = $this->generatePin();
                    $voicemail = new Voicemail();
                    $voicemail->synchronizeWithArray($voicemailBox);
                    $voicemail->save();

                    $voicemail->class_type = 'DeviceVoicemail';
                    $newDevice->Voicemail = $voicemail;
                }

                $devices[] = $newDevice;

                $loopIterator++;
        }
        try {
            $devices->save();
            $devices->free();
        } catch (Exception $e) {
            kohana::log('error', 'Unable to add sample devices! ' . $e->getMessage());
            return array('warnings' => array('Unable to add sample devices! ' . $e->getMessage()));
        }
    }

    private function generatePassword($length = 8)
    {
        $consonants = 'bdghjmnpqrstvz';
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
        $consonants .= '1234567890';
        
        $vowels = 'aeuy';
        $vowels .= "AEUY";

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
                if ($alt == 1) {
                        $password .= $consonants[(rand() % strlen($consonants))];
                        $alt = 0;
                } else {
                        $password .= $vowels[(rand() % strlen($vowels))];
                        $alt = 1;
                }
        }
        // at least one digit
        $password = substr_replace($password, rand(0, 9), rand(1, $length), 1);

        return $password;
    }

    private function generatePin($length = 4)
    {

        $pin = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            $pin .= rand(0,9);
        }

        return $pin;
    }

}
