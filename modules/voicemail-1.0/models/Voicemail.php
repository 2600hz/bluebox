<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemail extends Bluebox_Record
{
    public static $errors = array(
        'mailbox' => array (
            'required' => 'Please provide mailbox number',
            'onlydigits' => 'Must be digits only'
        ),
        'password' => array (
            'required' => 'Please provide mailbox password',
            'onlydigits' => 'Must be digits only',
            'length' => 'Must be longer than 3 digits',
        ),
        'email_address' => array (
            'required' => 'Please provide an email address'
        ),
        'mailbox' => array (
            'unique' => 'Mailbox is already in use'
        )
    );
    
     /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('voicemail_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 64, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('mailbox', 'string', 64, array('notnull' => true, 'unique' => true));
        $this->hasColumn('password', 'string', 64, array('notnull' => true));
        $this->hasColumn('audio_format', 'string', 4, array('default' => 'wav'));
    }

    public function setUp()
    {
        $this->hasMany('VoicemailNumber as Number', array('local' => 'voicemail_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        $this->actAs('Timestampable');
        $this->actAs('GenericStructure');
        $this->actAs('Polymorphic');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }

    public function preValidate(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        $errorStack = $this->getErrorStack();

        $validator = Bluebox_Controller::$validation;

        if (!empty($record['password']))
        {
            if (preg_match('/[^0-9]/', $record['password']))
            {
                $errorStack->add('password', 'onlydigits');
            }
        }

        if (!empty($record['registry']['email_all_messages']))
        {
            if (!preg_match("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)\b/", $record['registry']['email_address']))
            {
                $validator->add_error('voicemail[registry][email_address]', 'This is not a valid email');

                $errorStack->add('email_address', 'email');
            }
        }
    }
}
