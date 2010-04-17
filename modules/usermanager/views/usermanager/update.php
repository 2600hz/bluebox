<div id="usermanager_update_header" class="update usermanager module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="usermanager_update_form" class="update usermanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('User Information'); ?>

        <div class="field">
        <?php
            echo form::label('user[first_name]', 'First Name:');
            echo form::input('user[first_name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[last_name]', 'Last Name:');
            echo form::input('user[last_name]');
        ?>
        </div>

        <div class="field">
        <?php
            if (Router::$method == 'add')
            {
                $pwdAttr = array('for' => 'user[password]');
            } else {
                $pwdAttr = array('for' => 'user[password]', 'hint' => 'Leave blank to keep');
            }
            echo form::label($pwdAttr, 'Password:');
            echo form::password('user[password]', $password);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[confirm_password]', 'Confirm Password:');
            echo form::password('user[confirm_password]', $confirm_password);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[email_address]', 'Email:');
            echo form::input('user[email_address]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[location_id]', 'Location:');
            echo locations::dropdown('user[location_id]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views)) {
            echo subview::render($views, 'general');
        }

        $order = array('identification' ,'membership', 'routing', 'notification', 'features', 'other');
        if (isset($views)) {
            echo subview::renderAsSections($views, $order);
        }
    ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>