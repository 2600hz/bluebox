<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 * Copyright (C) 2005-2009, Darren Schreiber <d@d-man.org>
 *
 * Version: FPL 1.0 (a modified version of MPL 1.1)
 *
 * The contents of this file are subject to the FreePBX Public License Version
 * 1.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.freepbx.org/FPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FreePBX Modular Telephony Software Library / Application
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * Polycom.php - This class provides support for polycom phones
 *
 * @author Michael Phillips
 * @author Karl Anderson
 * @license FPL
 * @package FreePBX3
 */
class PolycomEndpoint extends EndpointDriver
{
    public function __construct($mac)
    {
        parent::__construct($mac);
        /**
         * FOLDER OPTIONS:
         * template - A directory relative to MODPATH whose contents should be placed
         *            into this directory
         * folderPermission - The mask to place on the folder and subfolders
         * overwrite - if set to false, existing folders/files in the provisioning directory
         *             will not be replaced
         * update - only replace an existing folder/file if the template is newer
         * shared - when a endpoint is deleted, leave these folder/files
         */
        // These are the direcotries that Polycom phones will use
        $this->directories['{mac}'] = array(
            'folderPermission' => 0755
        );
        $this->directories['{mac}/logs'] = array(
            'folderPermission' => 0777
        );
        $this->directories['{mac}/overrides'] = array(
            'folderPermission' => 0777
        );
        $this->directories['{mac}/contacts'] = array(
            'folderPermission' => 0777
        );
        $this->directories['polycom_firmware/'] = array(
            'template' => 'polycomdriver/firmware/',
            'folderPermission' => 0755,
            'update' => TRUE,
            'shared' => TRUE
        );
        /**
         * FILE OPTIONS:
         * foreach - Execute this for each line or phone, defaults to phone is missing
         * template - A directory relative to MODPATH of a template file, if missing or
         *             invalid an empty file will be created
         * filePermission - The mask to place on the file after creation
         * overwrite - if set to false, existing files in the provisioning directory
         *             will not be replaced
         * parse - Instead of scanning the contents for markers, just copy the file
         * update - only replace an existing file if the template is newer (only applies
         *          to non-parsed templates)
         * shared - when a endpoint is deleted, leave these folder/files
         */
        // Provision the phone
        $this->files['{mac}/phone.cfg'] = array(
            'foreach' => 'phone',
            'template' => 'polycomdriver/templates/phone.cfg',
            'filePermission' => 0755,
        );
        // Provision the lines
        $this->files['{mac}/reg_{line}.cfg'] = array(
            'foreach' => 'line',
            'template' => 'polycomdriver/templates/reg_{line}.cfg',
            'filePermission' => 0755,
        );
        // Point to the provisioning files
        $this->files['{mac}.cfg'] = array(
            'foreach' => 'phone',
            'template' => 'polycomdriver/templates/{mac}.cfg',
            'filePermission' => 0755,
        );
        // Ensure the bootrom is avaliable
        $this->files['bootrom.ld'] = array(
            'foreach' => 'phone',
            'template' => 'polycomdriver/firmware/bootrom.ld',
            'filePermission' => 0755,
            'parse' => FALSE,
            'update' => TRUE,
            'shared' => TRUE
        );
    }
    public function mapEndpointToPhone($endpoint, $options)
    {
        return array(
            'createdFiles' => '/' . implode(', /', $options['createdFiles'])
        );
    }
    public function mapEndpointToLine($endpointLine, $options)
    {
        return array(
            'address' => $endpointLine->Device->Sip->username,
            'auth_userId' => $endpointLine->Device->Sip->username,
            'auth_password' => $endpointLine->Device->Sip->password,
            'server_1_address' => $endpointLine->Device->User->Location->domain,
            'mwi_callBack' => $endpointLine->Device->Sip->username
        );
    }
}


//NOTES
//        echo form::open_fieldset(array('class' => 'endpoint_specific'));
//        echo form::legend('Message Center');
//
//        echo form::label($lineName .'[parameters][callback_mode]', 'Callback Mode:');
//        echo form::dropdown(array('name' => $lineName .'[parameters][callback_mode]'), array('Contact' => 'Contact', 'Registration' => 'Registration', 'Disabled' => 'Disabled'));
//
//        echo form::label($lineName .'[parameters][callback_contact]', 'Callback Contact:');
//        echo form::input(array('name' => $lineName .'[parameters][callback_contact]'));
//
//        echo html::br();
//
//        echo form::label($lineName .'[parameters][subscriber]', 'Subscriber:');
//        echo form::input(array('name' => $lineName .'[parameters][subscriber]'));
//
//        echo form::close_fieldset();