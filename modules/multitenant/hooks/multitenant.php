<?php

// We could optionally bind to core tables here... It makes things slightly slower though if those tables are never used.

class MultiTenantSetup {
    // We only
    function bootstrapMultiTenant ()
    {
        if (Router::$controller == 'user') {
            // Don't enforce accounts when doing user-related functions
            Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        }
    }
}

Event::add('system.post_routing', array(
    'MultiTenantSetup',
    'bootstrapMultiTenant'
));
