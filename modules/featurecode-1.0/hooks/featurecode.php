<?php

Event::add('numbermanager.collectNumberTargets', array('FeatureCodeManager', 'provideNumberTargets'));

dialplan::register('FeatureCode', 'conditioning');
//dialplan::register('FeatureCode', 'network');
//dialplan::register('FeatureCode', 'preRoute');
//dialplan::register('FeatureCode', 'postRoute');
