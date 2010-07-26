<div>
    <?php echo form::open(NULL, array('enctype' => "multipart/form-data")); ?>

    <?php echo form::open_section('Upload File'); ?>

    <div class="field">
        <?php
        echo form::label('upload[path]', 'Upload Path:');
        echo form::dropdown('upload[path]', filetree::file_tree_dir($soundPath, TRUE, '/8000$|16000$|32000$|48000$/'));
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload', 'Audio File (MP3 or WAV):');
        echo form::upload('upload');
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload[description]', 'Description:');
        echo form::input('upload[description]');
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload[8000]', 'Create 8kHz file');
        echo form::checkbox('upload[8000]', NULL, TRUE);
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload[16000]', 'Create 16kHz file');
        echo form::checkbox('upload[16000]', NULL, TRUE);
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload[32000]', 'Create 32kHz file');
        echo form::checkbox('upload[32000]', NULL, TRUE);
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('upload[48000]', 'Create 48kHz file');
        echo form::checkbox('upload[48000]', NULL, TRUE);
        ?>
    </div>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Upload'); ?>

    </div>


    <?php echo form::close_section(); ?>

    <?php echo form::close(); ?>
</div>
