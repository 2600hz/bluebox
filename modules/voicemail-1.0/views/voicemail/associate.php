<?php echo form::open_section('Voicemail Indicator'); ?>

    <div class="field">
    <?php
        echo form::label('voicemail[mwi_box]', 'Notify for Voicemail Box:');
        echo vm::dropdown('voicemail[mwi_box]', empty($voicemail['mwi_box']) ? NULL : $voicemail['mwi_box'] );
    ?>
    </div>

<?php echo form::close_section(); ?>