<?php

plugins::register('devicemanager/create', 'view', array('EndpointManager_Plugin', 'update'));

plugins::register('devicemanager/edit', 'view', array('EndpointManager_Plugin', 'update'));

plugins::register('devicemanager', 'save', array('EndpointManager_Plugin', 'save'));
