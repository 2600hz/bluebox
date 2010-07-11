<?php echo form::open_section('Timezone'); ?>

    <div class="field">
    <?php
        echo form::label('timezone[timezone]', 'Timezone');
        echo form::timezones('timezone[timezone]', $timezone['timezone']);
    ?>
    </div>

<?php echo form::close_section(); ?>