<?php

if (!Bluebox_Installer::is_installing()) {
    // This is hook enforces controller enable/disable permissions
    Event::add('system.post_controller_constructor', array(
        'Permission_Plugin',
        'bootstrapPermission'
    ));
}