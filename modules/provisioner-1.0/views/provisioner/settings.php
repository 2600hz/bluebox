<div id="provisioner_settings_header" class="settings provisioner module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="provisioner_settings_form" class="settings provisioner">
    <?php echo form::open(); ?>

    <?php echo form::open_section('File Creation'); ?>

        <div class="field">
        <?php
            echo form::label('write_to_disk', 'Create Files');
            echo form::checkbox(array('name' => 'write_to_disk', 'class' => 'determinant agent_for_write'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('endpointsetting[provision_path]', 'Provision Path');
            echo form::input(array('name' => 'endpointsetting[provision_path]', 'class' => 'dependent_positive rely_on_write'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('endpointsetting[chroot]', 'Chroot Mode');
            echo form::checkbox(array('name' => 'endpointsetting[chroot]', 'class' => 'dependent_positive rely_on_write'));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::render($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>