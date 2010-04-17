<?php
/* 
 * This software is licensed under the terms of the FPL (FreePBX Public License), a
 * modified version of the MPL (Mozilla Public License).
 * 
 * The full text of the license can be obtained at http://www.freepbx.org/fpl/.
 * 
 */

/**
 * Yate SIP Driver
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license FPL
 */
class Yate_Sip_Driver {
    /**
     * Indicate we support Yate SIP Devices and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $driver = Telephony::getDriver();
    }
}
