    <?php echo form::open_section('Playback'); ?>

        <div style="text-align: center;">
            <span>
                <audio src="<?php echo url_Core::file('mediafile/download/' .$mediafile['mediafile_id'] .'/1/' .$mediafile->downloadName(), TRUE); ?>" controls="controls" preload="none">
                    Your browser does not support HTML5 audio.
                </audio>
            </span>
        </div>

    <?php echo form::close_section(); ?>