<div id="mediafile_delete_header" class="delete mediafile module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="mediafile_delete_form" class="delete mediafile">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Confirm'); ?>

        <div class="delete_warning" style="text-align: center">

            <?php echo __('Are you sure you want to delete the media \'' .$mediafile['name'] .'\'?'); ?>

        </div>

    <?php echo form::close_section(); ?>

    <?php if(kohana::config('mediafile.hide_rate_folders', FALSE)): ?>

        <?php echo form::open_section('Rates'); ?>

            <?php $i = 0; foreach ($mediafile->get_resampled() as $file): ?>

                <div class="field">

                    <?php echo form::label('delete_media_files[' .$i .']', 'Remove ' .$file['rates'] .'hz file'); ?>

                    <?php echo form::checkbox('delete_media_files[' .$i++ .']', $file['mediafile_id'], TRUE); ?>
                    
                </div>

            <?php endforeach; ?>

        <?php echo form::close_section(); ?>

    <?php endif; ?>
    
    <?php 
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(form::BUTTONS_DELETE_CANCEL); ?>

</div>