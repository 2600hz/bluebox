<?php
    // Update any provisioner files when device is saved
    plugins::register('devicemanager', 'save', array('Provisioner_Plugin', 'refreshFiles'));
    plugins::register('devicemanager', 'delete', array('Provisioner_Plugin', 'removeLine'));
    plugins::register('devicemanager.success', 'delete', array('Provisioner_Plugin', 'successfulDelete'));

    plugins::register('provisioner', 'delete', array('Provisioner_Plugin', 'removeAllLines'));
    plugins::register('provisioner.success', 'delete', array('Provisioner_Plugin', 'removeFiles'));