<?php echo form::open_section('Interface Management'); ?>

    <div class="field">
    <?php
        echo form::label('Bind to Interface:');
        echo form::dropdown('sipinterface[sipinterface_id]', $options);
    ?>
    </div>

<?php echo form::close_section(); ?>