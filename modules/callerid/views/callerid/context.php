<?php echo form::open_section('Caller ID'); ?>

    <div class="field">
    <?php
        echo form::label(array('for' => 'callerid[external_number]'), 'Default outbound CID num:');
        echo callid::dropdown(array('name' => 'callerid[external_number]', 'nullOption' => 'None'));
    ?>
    </div>

<?php echo form::close_section(); ?>
