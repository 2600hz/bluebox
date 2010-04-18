<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * permission.php - Provides logic for the permission module
 *
 * @author Karl Anderson
 * @license LGPL
 * @package FreePBX3
 */

class Permission_Plugin extends FreePbx_Plugin
{
    public function bootstrapPermission() {
        if (Router::$method == 'disabled' && Router::$controller == 'permission') {
            return TRUE;
        }

        $allowed = Permissions::allow(Router::$controller, Router::$method);
        if(!$allowed) {
            url::redirect('permission/disabled');
        }
    }
}