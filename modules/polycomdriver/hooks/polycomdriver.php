<?php
plugins::register('provisioner/edit/polycomendpoint', 'view', array('Polycom_Plugin', 'update'));
plugins::register('provisioner/polycomendpoint', 'save', array('Polycom_Plugin', 'save'));