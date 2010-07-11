<div id="mediamanager_upload_header" class="mediamanager add upload module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="mediamanager_upload_form" class="mediamanager add">
    <?php echo form::open_multipart(); ?>

    <?php echo form::open_section('Select a file to upload'); ?>

        <p>
            <?php echo $maxUpload; ?>
        </p>

        <div class="field">
        <?php
            echo form::label('upload', 'File Selection');
            echo form::upload('upload', '');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('description', 'Description');
            echo form::textarea('mediamanager[description]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('replace', 'Replace file');
            echo form::checkbox('mediamanager[replace]');
        ?>
        </div>
    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>
        
    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
