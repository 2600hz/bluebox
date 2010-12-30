<?php echo form::open_section('ODBC Connection'); ?>

    <div class="field">
    <?php
        echo form::label('DSN:');
        echo OdbcManager::dsnSelector('odbc[odbc_id]', empty($odbc['odbc_id']) ? NULL : $odbc['odbc_id']);
    ?>
    </div>

<?php echo form::close_section(); ?>