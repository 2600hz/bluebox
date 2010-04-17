<?php echo form::open_section('Sign Up'); ?>

    <div class="field">
    <?php
        echo form::label('register[first_name]', 'First Name:');
        echo form::input('register[first_name]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('register[last_name]', 'Last Name:');
        echo form::input('register[last_name]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('register[email_address]', 'Email Address:');
        echo form::input('register[email_address]');
    ?>
    </div>

    <?php if (!Kohana::config('core.username_is_email')) : ?>
    <div class="field">
    <?php
        echo form::label('register[username]', 'Username:');
        echo form::input('register[username]');
    ?>
    </div>
    <?php endif; ?>

    <div class="field">
    <?php
        echo form::label('register[password]', 'Password:');
        echo form::password('register[password]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('confirm_password', 'Confirm Password:');
        echo form::password('confirm_password');
    ?>
    </div>

<?php echo form::close_section(); ?>

<div class="buttons form_bottom">
<?php
    // DO NOT change the name of the submit button - it is used in JavaScript CSS selectors
    echo form::submit(array('name' => 'action', 'class' => 'user_register_button small_green_button'), 'Register');
?>
</div>
