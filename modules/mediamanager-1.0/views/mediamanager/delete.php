<div id="mediamanager_delete_header" class="mediamanager delete module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="mediamanager_delete_form" class="mediamanager delete">
<?php if (empty($blocking_error)): ?>

    <?php echo form::open(); ?>

    <?php echo form::open_section('Confirm Deletion of File (' .$name .')'); ?>

    <?php echo __(sprintf('Are you sure you want to delete the file %s ?', $name)); ?>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'No'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Yes'); ?>
    </div>

    <?php echo form::close(); ?>
<?php endif; ?>
</div>