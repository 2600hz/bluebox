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

    <?php echo form::close(form::BUTTONS_DELETE_CANCEL); ?>

<?php endif; ?>
</div>