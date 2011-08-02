<?php echo form::open_section('Phone Provisioner'); ?>

    <div class="field">
    <?php
        echo form::label('provisioner[type]', 'Phone Type:');
        echo form::input('provisioner[type]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('provisioner[model]', 'Phone Model:');
        echo form::dropdown('provisioner[model]', array('GXP2000', 'GXP2020'));
    ?>
    </div>

    <div class="field">
    <?php
        echo html::anchor('provisioner/configure', 'Configure Phone', array('class' => 'qtipAjaxForm'));
    ?>
    </div>



<?php echo form::close_section(); ?>