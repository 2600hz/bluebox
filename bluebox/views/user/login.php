<?php echo form::open_section('Login'); ?>

    <div class="field">
    <?php
        if (Kohana::config('core.username_is_email')) {
            echo form::label('login[email_address]', 'Email Address: ');
            echo form::input('login[email_address]');
        } else {
            echo form::label('login[username]', 'Username: ');
            echo form::input('login[username]');
        }
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('login[password]', 'Password: ');
        echo form::password('login[password]');
    ?>
    </div>

<?php echo form::close_section(); ?>

<div class="buttons form_bottom">
<?php
    // DO NOT change the name of the submit button - it is used in JavaScript CSS selectors
    echo form::submit(array('name' => 'action', 'class' => 'user_login_button small_green_button'), 'Login');
?>
</div>
