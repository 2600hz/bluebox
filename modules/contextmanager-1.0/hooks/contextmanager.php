<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/edit', 'view', array('ContextManager_Plugin', 'index'));
    
    // in conditioning unroll sip loops
    //dialplan::register('Contextmanager', 'conditioning');

    // in postexecute insert our catch all handler
    //dialplan::register('Contextmanager', 'postexecute');

    // in pre-number insert the inbound prepend action (when approriate)
    //dialplan::register('Contextmanager', 'prenumber');