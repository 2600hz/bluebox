    <div class="field">
        <?php echo form::label($pluginvar .'[tts_voice]', 'Voice:'); ?>
        <?php echo tts::dropdown($pluginvar .'[tts_voice]'); ?>
    </div>

    <div class="field">
        <?php echo form::label($pluginvar .'[tts_text]', 'Text:'); ?>
        <?php echo form::textarea(array('name' => $pluginvar .'[tts_text]', 'style' => 'width:100%;')); ?>
    </div>