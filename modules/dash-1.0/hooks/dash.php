<?php
    Event::add('system.post_controller_constructor', array(
        'DashManager',
        'addCSS'
    ));


