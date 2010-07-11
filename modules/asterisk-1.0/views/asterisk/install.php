    <?php echo form::open_section('Telephony Configuration'); ?>

        <div class="field">
        <?php
            echo form::label('ast_root', 'Conf Directory:');
            echo form::input('ast_root');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Manger API'); ?>

        <div class="field">
        <?php
            echo form::label('ami_host', 'Manager Host:');
            echo form::input('ami_host');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('ami_port', 'Manager Port:');
            echo form::input('ami_port');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('ami_user', 'Manager Username:');
            echo form::input('ami_user');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('ami_pass', 'Manager Password:');
            echo form::input('ami_pass');
        ?>
        </div>

    <?php echo form::close_section(); ?>