<?php
    //plugins::register('ringgroup.edit', 'save', array('RingGroup_Plugin', 'checkChanges'));
    //plugins::register('ringgroup.success', 'save', array('RingGroup_Plugin', 'dirtyNumbers'));

    plugins::register('destinations/selector', 'view', array('RingGroup_Plugin', 'selector'));
