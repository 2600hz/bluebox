<div id="usermanager_update_header" class="update usermanager module_header">

    <h2><?php echo $title; ?></h2>

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
            echo form::label('user[user_type]', 'User Type:');
            echo usermanager::dropdownUserType('user[user_type]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[location_id]', 'Location:');
            echo locations::dropdown('user[location_id]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Credentials'); ?>

        <div class="field">
        <?php
            echo form::label('user[email_address]', 'Email:');
            echo form::input('user[email_address]');
        ?>
        </div>

        <div class="field">
        <?php
            if (Router::$method == 'create')
            {
                $pwdAttr = array('for' => 'user[create_password]');
            } else {
                $pwdAttr = array('for' => 'user[create_password]', 'hint' => 'Leave blank to keep');
            }
            echo form::label($pwdAttr, 'Password:');
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

    <?php
        if (isset($views))
        {
            echo subview::render($views);
        }
    ?>

    <?php if (users::$user->user_type == User::TYPE_SYSTEM_ADMIN) : ?>

        <?php jquery::addPlugin('spinner'); ?>
    
        <?php echo form::open_section('Debug'); ?>

            <div class="field">
            <?php
                echo form::label('user[debug_level]', 'UI Level:');
                echo form::input('user[debug_level]');
                javascript::codeBlock('$("#user_debug_level").spinner({max: 4, min: 0});');
            ?>
            </div>

        <?php echo form::close_section(); ?>

    <?php endif; ?>

    <?php echo form::close(TRUE); ?>
</div>

