<?php echo form::open_section('Auto-Provisioning'); ?>

    <div class="field">
    <?php
        echo form::label('endpointmanager[mac_address]', 'MAC Address:');
        echo form::dropdown('endpointmanager[mac_address]', $mac_addresses);
    ?>
    </div>

<?php echo form::close_section(); ?>