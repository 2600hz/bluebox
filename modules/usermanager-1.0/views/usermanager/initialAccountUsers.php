<?php echo form::open_section('Primary Account Admin'); ?>

    <div class="field">
    <?php
        echo form::label('user[first_name]', 'First Name:');
        echo form::input('user[first_name]', isset($user['first_name']) ? $user['first_name'] : 'Account');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('user[last_name]', 'Last Name:');
        echo form::input('user[last_name]', isset($user['last_name']) ? $user['last_name'] : 'Admin');
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
        echo form::label('user[create_password]', 'Password:');
        echo form::input('user[create_password]',
            isset($password) ? $password : NULL
        );
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('user[confirm_password]', 'Confirm Password:');
        echo form::input('user[confirm_password]',
            isset($confirm_password) ? $confirm_password : NULL
        );
    ?>
    </div>

<?php echo form::close_section(); ?>