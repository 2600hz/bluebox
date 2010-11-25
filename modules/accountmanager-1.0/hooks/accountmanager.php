<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.users.redirectInvalidUser', array('clearAccountMasquerades', 'cleanup'));

    class clearAccountMasquerades
    {
        public static function cleanup()
        {
            if (users::isAuthentic('account_id'))
            {
                return;
            }
            
            if (!users::isAuthentic('user_id'))
            {
                return;
            }
            
            $baseController = Session::instance()->get('ajax.base_controller', '');

            if (strcasecmp($baseController, 'accountmanager'))
            {
                kohana::log('debug', 'Account manager is restoring the account masquerade');
                
                users::restoreAccount();
            }
        }
    }