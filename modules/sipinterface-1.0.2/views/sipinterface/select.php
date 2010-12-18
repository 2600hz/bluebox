<?php echo form::open_section('Interface Management'); ?>

    <div class="field">
    <?php
        echo form::label('Bind to Interface:');

        $options = array(
            'name' => 'sipinterface[sipinterface_id]',
        );
        
        if ($base == 'trunk')
        {
            $options += array(
                'default_first' => FALSE,
                'unauth_before_auth' => TRUE
            );
        }
        else if ($base == 'location')
        {
            $options['null_option'] =
                'Default (' .SipInterface::get_default('name') .')';
        }

        echo sipinterfaces::dropdown($options);
    ?>
    </div>

<?php echo form::close_section(); ?>