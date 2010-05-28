<?php
    plugins::register('timeofday.edit', 'save', array('TimeOfDay_Plugin', 'checkChanges'));
    plugins::register('timeofday.success', 'save', array('TimeOfDay_Plugin', 'dirtyNumbers'));
