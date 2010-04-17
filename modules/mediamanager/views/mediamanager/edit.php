<div id="mediamanager_edit_header" class="mediamanager edit module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="mediamanager_edit_form" class="mediamanager edit">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Update file description'); ?>
    
        <div class="field">
        <?php
            echo form::label('mediamanager[description]', 'Description');
            echo form::textarea('mediamanager[description]');
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

