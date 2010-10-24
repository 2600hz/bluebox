<?php echo form::open_section('Media Handling Option'); ?>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'mediaoption[bypass_media]',
                'hint' => 'Try to connect the endpoint media directly',
                'help' => 'When checked the switching system will attempt to connect the RTP/media path directly, without participating in the stream.  This takes the server out of the audio stream, preventing touch tone detection, and also requires endpoint A and B to be able to reach each other directly. Endpoint A and B will see each other\'s IP addresses during SIP audio setup.<br><br>When unchecked (default), the switching system will stay in the RTP/media path for the duration of the call. This adds an extra hop for the audio to take, adding a delay, but can fix DTMF and Codec issues by allowing transcoding to occur on the switch side. In addition, you can also monitor the audio on the call for events such as touch-tones. <br><br>When enabled provides best call quality, reduces bandwidth consumption and processing overhead on the switching server.  However, it is more complicated to get to work correctly.'
            ),
            'Enable RTP/Media Bypass Mode:'
        );
        echo form::checkbox('mediaoption[bypass_media]');
    ?>
    </div>

<?php echo form::close_section(); ?>