<div id="accountmanager_update_header" class="update accountmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="accountmanager_update_form" class="update accountmanager">
    
    <?php echo form::open(); ?>

    <?php echo form::open_section('Account Information'); ?>

        <div class="field">
        <?php
            echo form::label('account[name]', 'Account Name:');
            echo form::input('account[name]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>
    
    <?php echo form::open_section('Account Status'); ?>
        <div class="field">
        <?php
            echo form::label('account[type]', 'Account Type:');
            echo form::dropdown('account[type]', Account::$types);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('account[status]', 'Account Active:');
            echo form::checkbox('account[status]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('account[registry][allow_login]', 'Allow Web Login:');
            echo form::checkbox('account[registry][allow_login]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>