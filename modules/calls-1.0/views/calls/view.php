<div id="calls_view_header" class="view calls module_header">

    <h2><?php echo __($title); ?></h2>

</div>

<div id="usermanager_update_form" class="view calls">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Call Detail'); ?>

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

</div>
