<?php echo form::open_section('MyPlugin'); ?>

    <div class="field">
    <?php
        echo form::label('myplugin[mydatafield1]', 'Data Field 1:');
        echo form::input('myplugin[mydatafield1]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('myplugin[mydatafield2]', 'Data Field 2:');
        echo form::input('myplugin[mydatafield2]');
    ?>
    </div>

<?php echo form::close_section(); ?>