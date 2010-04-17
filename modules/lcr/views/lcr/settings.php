<?php message::render(); ?>

<div id="lcr_settings_form" class="txt-left form settings lcr">
    <?php
    echo form::open();
    echo form::open_fieldset();
    echo form::legend('LCR Settings');

    echo form::label('lcr[odbcdsn]', 'ODBC DSN');
    echo form::input('lcr[odbcdsn]', $dsn, 'class="text"');
    echo html::br();


    echo form::submit('submit', 'Save');
    echo form::close_fieldset();
    echo form::close();
