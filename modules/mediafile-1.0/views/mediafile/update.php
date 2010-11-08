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

            <?php echo new View('mediafile/playback', array('mediafile' => $sample_rates[0])); ?>

        <?php endif; ?>

        <?php if (kohana::config('mediafile.file_details', TRUE)): ?>

            <?php echo new View('mediafile/details', array('mediafiles' => $sample_rates)); ?>
    
        <?php endif; ?>

    <?php endif; ?>

    <?php echo form::close(TRUE); ?>
    
</div>