<?php defined('SYSPATH') or die('No direct access allowed.');

class Conference extends Bluebox_Record
{    
    const TYPE_MEMBER = 0;
    const TYPE_MODERATOR = 1;

    public static $default_keymap = array(
        'vol talk dn' => '1',
        'vol talk zero' => '2',
        'vol talk up' => '3',
        'vol listen dn' => '4',
        'vol listen zero' => '5',
        'vol listen up' => '6',
        'energy dn' => '7',
        'energy equ' => '8',
        'energy up' => '9',
        'deaf mute' => '*',
        'mute' => '0'
    );

    public static $default_profile = array(
        'rate' => 32000,
        'interval' => 20,
        'energy-level' => 20,
        'sound-prefix' => '$${sounds_dir}/en/us/callie',
        //'ack-sound' => 'beep.wav',
        //'nack-sound' => 'beeperr.wav',
        'caller-controls' => 'default-keymap',
        'tts-engine' => 'flite',
        'tts-voice' => 'kal',
        'muted-sound' => 'conference/conf-muted.wav',
        'mute-detect-sound' => '',
        'max-members' => '',
        'max-members-sound' => '',
        'comfort-noise' => '1420',
        //'announce-count' => '',
        'suppress-events' => '',
        'verbose-events' => '',
        'unmuted-sound' => 'conference/conf-unmuted.wav',
        'alone-sound' => 'conference/conf-alone.wav',
        //'perpetual-sound' => 'perpetual.wav',
        'moh-sound' => 'silence',
        'enter-sound' => 'tone_stream://%(200,0,500,600,700)',
        'exit-sound' => 'tone_stream://%(500,0,300,200,100,50,25)',
        'kicked-sound' => 'conference/conf-kicked.wav',
        'locked-sound' => 'conference/conf-locked.wav',
        'is-locked-sound' => 'conference/conf-is-locked.wav',
        'is-unlocked-sound' => 'conference/conf-is-unlocked.wav',
        'pin-sound' => 'conference/conf-pin.wav',
        'bad-pin-sound' => 'conference/conf-bad-pin.wav',
        //'pin' => '',
        //'caller-id-name' => '',
        //'caller-id-number' => '',
        //'suppress-events' => '',
        'comfort-noise' => 'true',
        //'auto-record' => ''
    );

    public static $errors = array(
        'name' => array(
            'required' => 'Name is required.'
        )
    );

    function setTableDefinition()
    {
        $this->hasColumn('conference_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('controlls', 'array', 10000, array('default' => array()));
        $this->hasColumn('profile', 'array', 10000, array('default' => array()));
        $this->hasColumn('pins', 'array', 10000, array('default' => array()));
    }

    function setUp()
    {
        $this->hasMany('ConferenceNumber as Number', array('local' => 'conference_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }

    public function preValidate(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();
        
        $errorStack = $this->getErrorStack();

        $validator = Bluebox_Controller::$validation;

        foreach ($record['pins'] as $key => $pin)
        {
            if (!empty($pin))
            {
                if (preg_match('/[^0-9]/', $pin))
                {
                    $validator->add_error('conference[pins][' .$key .']', 'Please provide only numbers');

                    $errorStack->add('password', 'digitsonly');
                }
            }
        }
    }
}
