<div id="user_profile_header" class="profile user module_header">
    <h2><?php echo __('Profile'); ?></h2>
</div>

<div id="devicemanager_update_form" class="update devicemanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Change Password'); ?>

        <div class="field">
        <?php
            echo form::label('user[old_password]', 'Old Password:');
            echo form::password('user[old_password]', $old_password);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[new_password]', 'New Password:');
            echo form::password('user[new_password]', $new_password);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[confirm_password]', 'Confirm Password:');
            echo form::password('user[confirm_password]', $confirm_password);
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