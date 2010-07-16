<?php
    // Delete the actuall file on delete
    plugins::register('mediamanager.success', 'delete', array('MediaManager_Plugin', 'deleteFile'));

    plugins::register('bluebox.maintenance', '', array('MediaManager', 'scanForChanges'));
    