    <?php echo form::open_section('Master Administration Account'); ?>

        <div class="field">
        <?php
            echo form::label('adminEmailAddress', 'Email Address:');
            echo form::input('adminEmailAddress');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('adminPassword', 'Password:');
            echo form::password('adminPassword');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('adminConfirmPassword', 'Confirm Password:');
            echo form::password('adminConfirmPassword');
        ?>
        </div>

    <?php echo form::close_section(); ?>