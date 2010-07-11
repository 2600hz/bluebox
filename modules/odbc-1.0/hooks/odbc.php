<?php
plugins::register('sipinterface/add', 'view', array('Odbc_Plugin', 'update'));
plugins::register('sipinterface/edit', 'view', array('Odbc_Plugin', 'update'));
plugins::register('sipinterface', 'save', array('Odbc_Plugin', 'save'));
