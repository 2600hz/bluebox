<?php echo form::open_section('Voicemail'); ?>
    <div class="field">
        <?php
            echo form::label('voicemail[voicemail_id]', 'Voicemail Box');
            echo form::dropdown('voicemail[voicemail_id]', $voicemailBoxes);
        ?>
    </div>
<?php echo form::close_section(); ?>