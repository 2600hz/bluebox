<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * skins.php - Skin manager/helper. Handles skinning of the interface with the correct user/site skin.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class skins {
    /**
     * @var object the singleton implementation
     */
    private static $instance = NULL;

    /**
     *
     * @var string Name of the skin to be used.
     */
    protected static $skin = NULL;

    /**
     *
     * @var object The site object pulled from doctrine.
     */
    protected static $site = NULL;


    public static function initialize($autoDetect = TRUE)
    {
        if (empty(skins::$instance)) {
            skins::$instance = new skins();
        }

        if (($autoDetect) and (!self::$skin)) {
            $site = self::discover();
            self::$site = $site;
            self::$skin = rtrim($site->Skin->location, '/') .'/';
        }

        return skins::$instance;
    }

    public static function discover($skinId = NULL)
    {
        self::initialize(FALSE);

        $query = Doctrine_Query::create()
            ->select('s.homepage, k.name, k.location')
            ->from('Site s')
            ->orderBy('s.wildcard DESC')    // This is important - it places all wildcard entries LAST. We only use the first result in the event of multiple matches
            ->leftJoin('s.Skin k');

        // Was a specific skin requested? If not, use the URL loaded to identify which skin to use
        if ($skinId) {
            $query = $query->where('s.skin_id = ?', $skinId);
        } else {
            // Find skin based on URL or IP
            $host = $_SERVER['SERVER_NAME'];

            /*
             * TODO: IP-based skin support is not yet enabled. Need to decide precedence when a hostname exists
             *

            if (isset($_SERVER['SERVER_ADDR'])) {
                $ipaddress = $_SERVER['SERVER_ADDR']; // Optionally, check for an IP match. This does not work on IIS 6
            } else {
                $ipAddress = gethostbyname($_SERVER['SERVER_NAME']); // Fix for IIS 6
            }
             */

            $query = $query->where('s.url LIKE ?', '%' . $host);
        }

        $sites = $query->execute();

        //TODO: Fix this. Why is it being difficult?
        foreach ($sites as $site) {
        }

        // Did we find a site?
        if (!isset($site)) {
            // No site found - use the default
            $query = Doctrine_Query::create()
                ->select('s.homepage, k.name, k.location')
                ->from('Site s')
                ->where('s.default = ?', TRUE)
                ->leftJoin('s.Skin k');
                
            $sites = $query->execute();
            //TODO: Fix this. Why is it being difficult?
            foreach ($sites as $site){
            }
        }

        return $site;
    }

    /**
     * Get the currently set skin's name
     * @return string Name of current skin
     */
    public static function getSkin()
    {
        self::initialize();

        return self::$skin;
    }

    /**
     * Set the currently used skin's name
     * @param string $skin Set the skin we're going to use. Useful if a particular plugin needs a special skin (like a storefront)
     */
    public static function setSkin($skin)
    {
        self::initialize(FALSE);    // Do not try to auto-detect - we are setting manually

        self::$skin = $skin;
    }

}
