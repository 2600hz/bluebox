<?php
    echo form::open_fieldset();

    echo form::legend('mISDN Trunk Settings');

    echo form::label('misdn[provider]', 'ISDN Provider:');
    echo form::input(array('name' => 'misdn[provider]'));

    echo form::label('misdn[port]', 'mISDN Card Port:');
    echo form::input(array('name' => 'misdn[port]'));

    echo form::close_fieldset();