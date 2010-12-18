<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * SipInterface.php - SipInterface class
 *
 * This is a FreeSWITCH specific class. It lets you define public interfaces and assign trunks/gateways to those interfaces.
 * An "interface" is a SIP profile in FreeSWITCH.
 *
 * Created on Jul 21, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage SipInterface
 */
class SipInterface extends Bluebox_Record
{
    public static $errors = array(
        'name' => array(
            'required' => 'Name is required.',
            'default' => 'Invalid name.'
        ),
        'ip_address' => array(
            'default' => 'Invalid IP address.'
        ),
        'port' => array(
            'default' => 'Invalid port number.'
        ),
        'context' => array(
            'default' => 'Invalid selection.'
        )
    );

    private static $nested = FALSE;

    // This is only used during transactions were there are not interfaces already (IE: install)
    private static $default_id = NULL;

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('sipinterface_id', 'integer', 11, array(
                'unsigned' => true,
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            )
        );

        $this->hasColumn('name', 'string', 100, array(
                'notnull' => true, 
                'notblank' => true
            )
        );

        $this->hasColumn('multiple', 'boolean');

        $this->hasColumn('ip_address', 'string', 40, array(
                'default' => ''
            )
        );

        $this->hasColumn('port', 'integer', 11, array(
                'unsigned' => true,
                'default' => 5060
            )
        );

        $this->hasColumn('ext_ip_address', 'string', 40, array(
                'default' => ''
            )
        );

        $this->hasColumn('auth', 'boolean', NULL, array(
                'default' => true
            )
        );

        $this->hasColumn('nat_type', 'integer', 11, array(
                'default' => 0
            )
        );

        // List of IPs/Hosts/Domains to be used when detecting NAT
        $this->hasColumn('nat_net_list_id', 'integer', 11, array(
                'unsigned' => true,
            )
        );

        // List of IPs/Hosts/Domains applied to incoming INVITEs, skips authentication
        $this->hasColumn('inbound_net_list_id', 'integer', 11, array(
                'unsigned' => true,
            )
        );

        // List of IPs/Hosts/Domains applied to incoming REGISTERs, skips authentication
        $this->hasColumn('register_net_list_id', 'integer', 11, array(
                'unsigned' => true,
            )
        );

        // Default routing context/context
        $this->hasColumn('context_id', 'integer', 11, array(
                'unsigned' => true
            )
        );
        
        // IP and port combinations need to be unique
        $this->index('ip_port_idx', array(
                'fields' => array('ip_address', 'port'),
                'type' => 'unique',
            )
        );
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Context', array('local' => 'context_id', 'foreign' => 'context_id', 'onDelete' => 'SET NULL'));

        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }

    /**
     * TODO: So I need the default_sipinterface_id to be a 'global' setting across all interfaces
     * and since we have nowhere to save that I am going to cheat and put it on all interfaces.
     * this will keep them in sync but a better solution needs to be found...
     */
    public function postSave()
    {
        if (self::$nested)
        {
            return;
        }

        self::$nested = TRUE;

        $sipinterfaces = Doctrine::getTable('SipInterface')->findAll();

        if(!$default_sipinterface_id = arr::get($this->get('registry'), 'default_sipinterface_id'))
        {
            if (!self::$default_id)
            {
                if (!$default_sipinterface_id = self::get_default('sipinterface_id'))
                {
                    self::$default_id = $this->get('sipinterface_id');
                }
            }

            $default_sipinterface_id = self::$default_id;
        }
        
        foreach($sipinterfaces as $sipinterface)
        {
            $registry = $sipinterface['registry'];

            $registry['default_sipinterface_id'] = $default_sipinterface_id;

            $sipinterface['registry'] = $registry;

            $sipinterface->save();
        }

        self::$nested = FALSE;
    }

    public static function get_default($paths = NULL)
    {
        $sipinterfaces = Doctrine::getTable('SipInterface')->findAll();

        if ($sipinterfaces->count())
        {
            $default_sipinterface = array();

            foreach ($sipinterfaces as $sipinterface)
            {
                if($default_sipinterface_id = arr::get($sipinterface, 'registry', 'default_sipinterface_id'))
                {
                    $default_sipinterface = Doctrine::getTable('SipInterface')->find($default_sipinterface_id);

                    if ($default_sipinterface)
                    {
                        break;
                    }
                }
            }

            if (!$paths)
            {
                return empty($default_sipinterface) ? FALSE : $default_sipinterface;
            }

            $paths = func_get_args();

            return arr::get_array($default_sipinterface, $paths);
        }

        return FALSE;
    }
}