<?php echo form::open_section('Auto-Provisioning'); ?>

    <div class="field">
    <?php
        echo form::label('endpointdevice[mac_address]', 'MAC Address:');
        echo form::input('endpointdevice[mac_address]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('endpointdevice[brand]', 'Brand:');
        echo form::input('endpointdevice[brand]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('endpointdevice[model]', 'Model:');
        echo form::input('endpointdevice[model]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label();
        echo form::button('Configure', 'Configure');
    ?>
    </div>

<?php echo form::close_section(); ?>