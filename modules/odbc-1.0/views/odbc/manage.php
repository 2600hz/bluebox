<?php echo form::open_section('ODBC Connection'); ?>

    <div class="field">
    <?php
        echo form::label('Use ODBC?');
        echo form::checkbox('odbcmap[enable_odbc]', TRUE, $enable_odbc);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('DSN:');
        echo OdbcManager::dsnSelector('odbcmap[odbc_id]', $odbcmap['odbc_id']);
    ?>
    </div>

<?php echo form::close_section(); ?>

