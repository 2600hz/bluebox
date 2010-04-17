<?php echo form::open_section('Media Handling Option'); ?>

    <div class="field">
    <?php
        echo form::label(array('for' => 'mediaoption[media_workaround]',
                               'hint' => 'Whether to stay in the media path or not',
                               'help' => 'When disabled, the switching system will stay in the RTP/media path for the duration of the call. This adds an extra hop for the audio to take, adding a delay, but can fix DTMF and Codec issues by allowing transcoding to occur on the switch side. In addition, you can also monitor the audio on the call for events such as touch-tones. <br><br>When enabled, provides best call quality and reduces bandwidth consumption and processing overhead on the switching server. Enabling bypass mode allows audio signals to flow directly between endpoint A and B. Note that this takes the server out of the audio stream, preventing touch tone detection, and also requires endpoint A and B to be able to reach each other directly. Endpoint A and B will see each other\'s IP addresses during SIP audio setup.'),
                         'RTP/Media Bypass Mode:');
        echo form::checkbox(array('name' => 'mediaoption[media_workaround]', 'unchecked' => 0), 1);
    ?>
    </div>

<?php echo form::close_section(); ?>