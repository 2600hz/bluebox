<div>
    <?php echo form::open(); ?>

    <?php echo form::open_section('Create Folder'); ?>

    <div class="field">
        <?php
        echo form::label('path', 'Upload Path:');
echo form::dropdown('path', array_merge(array('' => '(root)'), filetree::file_tree_dir($soundPath, TRUE, '/8000$|16000$|32000$|48000$/')), $reqPath);
        ?>
    </div>

    <div class="field">
        <?php
        echo form::label('newfolder', 'Folder Name:');
        echo form::input('newfolder');
        ?>
    </div>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Create Folder'); ?>

    </div>


    <?php echo form::close_section(); ?>

    <?php echo form::close(); ?>
</div>
