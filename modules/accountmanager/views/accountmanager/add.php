<div id="accountmanager_add_header" class="add accountmanager module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="accountmanager_add_form" class="add accountmanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Account Information'); ?>

    <div class="field">
    <?php
        echo form::label('account[name]', 'Account Name:');
        echo form::input('account[name]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('account[type]', 'Account Type:');
        echo form::dropdown('account[type]', Account::$types);
    ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Account Setup'); ?>

    <div class="field">
    <?php
        echo form::label('account_domain', 'Account Domain:');
        echo form::input('account_domain');
        echo '  <small>Clients register their devices to this domain via sipUser@domain.com</small>';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array('for' => 'account_url', 'hint' => 'Full URL to get to this client (optional)'), 'Access URL:');
        echo form::input('account_url');
        echo ' <small>ex: http://client.mycompany.com</small>';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array('for' => 'account_basedata', 'hint' => 'Contexts, User Accounts, Default Skin'), 'Add Base Data:');
        echo form::checkbox('account_basedata', TRUE, TRUE);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array('for' => 'account_sampledata', 'hint' => 'Numbers, Devices, Conferences, etc.'), 'Add Sample Data:');
        echo form::checkbox('account_sampledata', TRUE, TRUE);
    ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Account Primary'); ?>

    <div class="field">
    <?php
        echo form::label('account_username', 'Master Username:');
        echo form::input('account_username');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('account_password', 'Master User Password:');
        echo form::input('account_password');
    ?>
    </div>

    <?php echo form::close_section(); ?>
    
    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>