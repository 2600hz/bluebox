<div id="blueboxmanager_settings_header" class="settings blueboxmanager module_header">
    <h2><?php echo __('Module Settings'); ?></h2>
</div>


<div id="blueboxmanager_settings_form" class="settings blueboxmanager">
    <?php echo form::open(); ?>

    <?php if (isset($views)) echo subview::render($views); ?>

    <?php if (empty($views[0]->hideParentSubmit)) : ?>
        <div class="buttons form_bottom">
            <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
            <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
        </div>
    <?php endif; ?>
    
    <?php echo form::close();?>
</div>