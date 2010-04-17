<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
plugins::register('accountmanager/add', 'save', array('AccountManager_Plugin', 'createAccountAdmin'));
plugins::register('accountmanager/success', 'save', array('AccountManager_Plugin', 'runTenantSetup'));

plugins::register('accountmanager', 'delete', array('AccountManager_Plugin', 'removeAssociated'));
?>
