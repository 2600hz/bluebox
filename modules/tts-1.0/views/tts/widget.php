    <div class="field">
        <?php echo form::label('media[tts_voice]', 'Voice:'); ?>
        <?php echo tts::dropdown('media[tts_voice]'); ?>
    </div>

    <div class="field">
        <?php echo form::label('media[tts_text]', 'Text:'); ?>
        <?php echo form::textarea(array('name' => 'media[tts_text]', 'style' => 'width:100%;')); ?>
    </div>