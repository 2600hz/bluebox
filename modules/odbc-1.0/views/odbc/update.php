<div id="odbc_update_header" class="update odbc module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="odbc_update_form" class="update odbc">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Connection Information'); ?>

        <div class="field">
        <?php
            echo form::label('odbc[dsn_name]', 'DSN Name');
            echo form::input('odbc[dsn_name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[database]', 'Database');
            echo form::input('odbc[database]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[user]', 'User Name');
            echo form::input('odbc[user]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[pass]', 'Password');
            echo form::input('odbc[pass]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[host]', 'Host');
            echo form::input('odbc[host]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[description]', 'Description');
            echo form::textarea('odbc[description]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'odbc[port]','hint' => 'Leave blank for default'), 'Port:');
            echo form::input('odbc[port]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('odbc[type]', 'Type (driver):');
            echo OdbcManager::dbmsSelector('odbc[type]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <?php echo form::close(TRUE); ?>
</div>