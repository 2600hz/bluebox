<?php echo form::open_section('Timezone'); ?>

    <div class="field">
    <?php
        echo form::label('timezone[timezone]', 'Timezone');
        echo timezone::dropdown('timezone[timezone]', empty($timezone['timezone']) ? NULL : $timezone['timezone'] );
    ?>
    </div>

<?php echo form::close_section(); ?>