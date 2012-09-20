<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('numbermanager/create', 'view', array('Fax_Plugin', 'update'));
    plugins::register('numbermanager/edit', 'view', array('Fax_Plugin', 'update'));
	plugins::register('numbermanager', 'save', array('Fax_Plugin', 'save'));
	
    Event::add('numbermanager.collectNumberTargets', array('Fax_Plugin', 'provideNumberTargets'));

    dialplan::register('FaxProfile', 'network');
    dialplan::register('FaxProfile', 'conditioning');
    dialplan::register('FaxProfile', 'preroute');
    dialplan::register('FaxProfile', 'postroute');
    dialplan::register('FaxProfile', 'preanswer');
    dialplan::register('FaxProfile', 'postanswer');
    dialplan::register('FaxProfile', 'prenumber');
    dialplan::register('FaxProfile', 'main');
    dialplan::register('FaxProfile', 'postnumber');
    dialplan::register('FaxProfile', 'catchall');
    dialplan::register('FaxProfile', 'postexecute');    

?>