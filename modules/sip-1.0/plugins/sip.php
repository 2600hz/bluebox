<?php defined('SYSPATH') or die('No direct access allowed.');

class Sip_Plugin extends Bluebox_Plugin
{
    const SIP_FORMAT_DIGITS = 1;
    const SIP_FORMAT_E164 = 2;
    const SIP_FORMAT_USER = 4;

    protected $name = 'sip';

    public static function getInviteFormats()
    {
        return array(
            self::SIP_FORMAT_DIGITS => '10 Digits@ip.address',
            self::SIP_FORMAT_E164 => 'E.164 (+1)@ip.address',
            self::SIP_FORMAT_USER => 'username@ip.address'
        );
    }

    public static function getCIDFormats()
    {
        return array(
            self::SIP_FORMAT_DIGITS => '10 Digits',
            self::SIP_FORMAT_E164 => 'E.164 (+1)',
        );
    }

    public function provideTrunkType()
    {
        $this->supportedTrunkTypes['sip'] = 'Sip Interface';
    }

    public function updateTrunk()
    {
        if (!$this->viewSetup())
        {
            return FALSE;
        }

        if (!$this->loadViewData())
        {
            return FALSE;
        }

        $this->subview->trunk_options = TRUE;

        if (!$this->addSubView())
        {
            return FALSE;
        }

        return TRUE;
    }
    
    protected function addPluginData()
    {
        $dialstring = '{presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}';

        $pluginInjection = array('dial-string' => $dialstring);

        $this->pluginData = arr::merge($this->pluginData, $pluginInjection);

	return parent::addPluginData();
    }
    
    protected function validate($data, $validator)
    {
        $base = $this->getBaseModelObject();

        if ($base instanceof Device)
        {
            // The password can not be empty if it is submitted
            if (( ! isset($data['password'])) OR (empty($data['password'])))
            {
                $validator->add_error('sip[password]', 'The SIP password can not be blank');
            }

            // The username can not be empty if it is submitted
            if (( ! isset($data['username'])) OR (empty($data['username'])))
            {
                $validator->add_error('sip[username]', 'The SIP username can not be blank');
                // if the username is blank stop now
                return false;
            }

            // Get all devices
            $devices = Doctrine_Query::create()
                ->select('d.plugins')
                ->from('Device d')
                ->execute(array(), Doctrine::HYDRATE_ARRAY);

            // Check if this sip username is in use in the current account
            // NOTE: This is nasty but a trade-off for far greater gains
            foreach ($devices as $device)
            {
                if (empty($device['plugins']['sip']['username']))
                {
                    continue;
                }

                if ($base['device_id'] == $device['device_id'])
                {
                    continue;
                }

                if ($device['plugins']['sip']['username'] == $data['username'])
                {
                    $validator->add_error('sip[username]', 'The SIP username already exists');
                }
            }
        }
    }
}
