<?php echo form::open_section('Call Intercept'); ?>
    <div class="field">
        <?php echo form::label(array('for' => 'device[registry][inbound_intercept_group]',
                                     'help' => 'Calls to this device can be intercepted by this Intercept Group'),
                                     'Belongs to Intercept Group:');
              echo form::input('callintercept[inbound_intercept_group]');
        ?>
    </div>

    <div class="field">
        <?php echo form::label(array('for' => 'device[registry][outbound_intercept_group]',
                                     'help' => 'This device can intercept calls from these Intercept Groups (a comma separated list)'),
                                     'Can Intercept These Groups:');
              echo form::input('callintercept[outbound_intercept_group]');
        ?>
    </div>
<?php echo form::close_section(); ?>
