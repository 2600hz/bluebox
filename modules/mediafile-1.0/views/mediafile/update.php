<div id="mediacollection_update_header" class="update mediacollection module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="mediacollection_update_form" class="txt-left form mediacollection updates">

    <?php echo form::open_multipart(); ?>

    <?php echo form::open_section('Identification'); ?>

        <div class="field">
            <?php echo form::label('mediafile[name]', 'Name:'); ?>
            <?php echo form::input('mediafile[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('mediafile[description]', 'Description:'); ?>
            <?php echo form::textarea('mediafile[description]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (!strcasecmp(Router::$method, 'create')): ?>

        <?php echo form::open_section('File'); ?>
    
            <div class="field">
                <?php echo form::label('mediafile[upload]', 'Audio File (MP3 or WAV):'); ?>
                <?php echo form::upload('mediafile[upload]'); ?>
            </div>

        <?php echo form::close_section(); ?>

    <?php else : ?>

        <?php echo form::open_section('Media Information'); ?>

            <div class="field">
                <label class="label">Path: </label>
                <span><?php echo $mediafile->filepath(TRUE, FALSE); ?></span>
            </div>

            <div class="field">
                <?php echo form::label(array('for' => 'sample_rates', 'hint' => 'Click to Download'), 'Sample Rates:'); ?>
                <span>
                    <?php foreach ($sample_rates as $file): ?>
                        <?php echo html::anchor('mediafile/download/' .$file['mediafile_id'], $file['rates'] . 'hz') . '&nbsp;&nbsp;';?>
                    <?php endforeach; ?>
                </span>
            </div>

        <?php echo form::close_section(); ?>

        <?php if (kohana::config('mediafile.playback', TRUE)): ?>

            <?php $file = end(arr::smart_cast($sample_rates)); ?>

            <?php echo form::open_section('Playback'); ?>
    
                <div style="text-align: center;">
                    <span>
                        <audio src="<?php echo url_Core::file('mediafile/download/' .$file['mediafile_id'] .'/1', TRUE); ?>" controls="controls" preload="none">Your browser does not support HTML5 audio.</audio>
                    </span>
                </div>

            <?php echo form::close_section(); ?>

        <?php endif; ?>

        <?php if (kohana::config('mediafile.file_details', TRUE)): ?>

            <?php echo form::open_section('File'); ?>

                <div id="sample_rate_tabs">

                    <?php if (count($sample_rates) > 1): ?>

                        <ul>
                             <?php foreach ($sample_rates as $file): ?>

                                 <li>
                                     <a href="#sample_rate_<?php echo $file['rates']; ?>">
                                         <span><?php echo $file['rates']; ?></span>
                                     </a>
                                 </li>

                             <?php endforeach; ?>
                        </ul>

                    <?php endif; ?>

                    <?php foreach ($sample_rates as $file): ?>

                        <div id="sample_rate_<?php echo $file['rates']; ?>">

                            <?php echo html::anchor('mediafile/delete/' .$file['mediafile_id'], 'Delete', array('style' => 'float:right;')); ?>

                            <div class="field">
                                <label class="label">Length: </label>
                                <span><?php echo $file['length']; ?> sec</span>
                            </div>

                            <div class="field">
                                <label class="label">Filesize: </label>
                                <span><?php echo number_format($file['size'], 0); ?> bytes</span>
                            </div>

                            <div class="field">
                                <label class="label">Type: </label>
                                <span><?php echo $file['type'] . ' (' .$file['bits'] .' bit)'; ?></span>
                            </div>

                            <div class="field">
                                <label class="label">Channels: </label>
                                <span><?php echo $file['channels']; ?></span>
                            </div>

                            <?php if (kohana::config('mediafile.visualization', FALSE)): ?>

                                <div class="field" style="text-align:center">
                                    <span>
                                        <?php echo html::image(array('src' => 'mediafile/visualize/' .$mediafile['mediafile_id'], 'width' => '400px', 'height' => '250px'), NULL, TRUE); ?>
                                    </span>
                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php echo form::close_section(); ?>
    
        <?php endif; ?>

    <?php endif; ?>

    <?php echo form::close(TRUE); ?>
    
</div>

<?php
    if (count($sample_rates) > 1)
    {
        jquery::addPlugin('tabs');
        javascript::codeBlock('$("#sample_rate_tabs").tabs();');
    }
?>