<?php
// Add a hook for managing users within the device manager
plugins::register('devicemanager/index', 'view', array('UserManager_Plugin', 'index'));
plugins::register('locationmanager/edit', 'view', array('UserManager_Plugin', 'edit'));
