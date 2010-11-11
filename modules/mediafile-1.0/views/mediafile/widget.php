
    <div class="field">
        <?php echo form::label('media[mediafile]', 'Media:'); ?>
        <?php echo mediafiles::dropdown(array('name' => 'media[mediafile]', 'id' => 'media_widget_file_list')); ?>
        &nbsp;&nbsp;<?php echo html::anchor('mediafile/create', 'Upload New Media', array('class' => 'qtipAjaxForm')); ?>
    </div>

    <div class="field">
        <?php echo form::label('mediafile_description', 'Description:'); ?>
        <?php echo form::textarea(array('name' => 'mediafile_description', 'readonly' => 'readonly')); ?>
    </div>

    <?php javascript::codeBlock(); ?>

        $('#media_widget_file_list').change(function () {
            $('#mediafile_description')
                .text('Please Wait . . .')
                .load('/mediafile/description/' + $(this).val());
        }).trigger('change');

    <?php javascript::blockEnd(); ?>