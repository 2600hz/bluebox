<div id="mediacollection_update_header" class="update mediacollection module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="mediacollection_update_form" class="txt-left form mediacollection updates">

    <?php echo form::open_multipart(); ?>

    <?php echo form::open_section('Media Information'); ?>

        <div class="field">
            <?php echo form::label('mediafile[name]', 'Name:'); ?>
            <?php echo form::input('mediafile[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('mediafile[description]', 'Description:'); ?>
            <?php echo form::textarea('mediafile[description]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('File'); ?>

        <?php if (!strcasecmp(Router::$method, 'create')): ?>

            <div class="field">
                <?php echo form::label('mediafile[upload]', 'Audio File (MP3 or WAV):'); ?>
                <?php echo form::upload('mediafile[upload]'); ?>
            </div>

        <?php else : ?>

            <div class="field">
                <label class="label">Filepath: </label>
                <span><?php echo $mediafile->filepath(TRUE, FALSE); ?></span>
            </div>

            <?php if (!kohana::config('mediafile.hide_rate_folders')) :?>

                <div class="field">
                    <label class="label">Filesize: </label>
                    <span><?php echo number_format($mediafile['size'], 0); ?> bytes</span>
                </div>

                <div class="field">
                    <label class="label">Type: </label>
                    <span><?php echo $mediafile['type'] . ' (' .$mediafile['bits'] .' bit)'; ?></span>
                </div>

                <div class="field">
                    <label class="label">Channels: </label>
                    <span><?php echo $mediafile['channels']; ?></span>
                </div>

            <?php endif; ?>

            <div class="field">
                <label class="label">Sample Rate: </label>
                <span>
                    <?php foreach ($sample_rates as $mediafile_id => $rate): ?>
                        <?php echo html::anchor('mediafile/download/' .$mediafile_id, $rate . 'hz') . '&nbsp;&nbsp;';?>
                    <?php endforeach; ?>
                    (Click to Download)
                </span>
            </div>
    
        <?php endif; ?>
    
    <?php echo form::close_section(); ?>

    <?php if (strcasecmp(Router::$method, 'create')): ?>

        <?php if (kohana::config('mediafile.playback', TRUE)): ?>

            <?php echo form::open_section('Playback'); ?>

                    <div class="field" style="text-align:center">
                        <span>
                            <audio src="<?php echo url_Core::file('mediafile/download/' .$mediafile_id. '/1', TRUE); ?>" controls="controls">Your browser does not support HTML5 audio.</audio>
                        </span>
                    </div>

            <?php echo form::close_section(); ?>

        <?php endif; ?>

        <?php if (kohana::config('mediafile.visualization', FALSE)): ?>

            <?php echo form::open_section('Visualization'); ?>

                    <div class="field" style="text-align:center">
                        <span>
                            <?php echo html::image(array('src' => 'mediafile/visualize/' .$mediafile['mediafile_id'], 'width' => '400px', 'height' => '250px'), NULL, TRUE); ?>
                        </span>
                    </div>

            <?php echo form::close_section(); ?>

        <?php endif; ?>

    <?php endif; ?>

    <?php echo form::close(TRUE); ?>
    
</div>