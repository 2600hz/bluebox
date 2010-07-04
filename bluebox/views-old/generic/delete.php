<div id="<?php echo $baseModel; ?>_delete_header" class="delete <?php echo $baseModel; ?> module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="<?php echo $baseModel; ?>_delete_form" class="delete <?php echo $baseModel; ?>">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Confirm'); ?>

    <div class="delete_warning">
        <?php i18n('Are you sure you want to delete the %1$s %2$s?', $baseModel, $name)->sprintf()->e(); ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'cancel', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'confirm', 'class' => 'save small_green_button'), 'Delete'); ?>
    </div>

    <?php echo form::close(); ?>
</div>