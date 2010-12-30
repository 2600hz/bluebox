<?php echo form::open_section('Auto-Provisioning'); ?>

    <div class="field">
    <?php
        echo form::label('endpointdevice[mac_address]', 'MAC Address:');
        echo form::input('endpointdevice[mac_address]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('endpointdevice[brand]', 'Phone:');
        include(MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . "functions.php");

        $endpoint = new endpointman();

        $list = $endpoint->get_devices_list();

        echo form::dropdown('endpointdevice[brand]', $list);

    ?>
    </div>

    <div class="field">
    <?php
        echo form::label();
        echo form::button('Configure', 'Configure');
    ?>
    </div>

<?php echo form::close_section(); ?>