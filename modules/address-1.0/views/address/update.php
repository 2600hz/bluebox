<?php echo form::open_section('Address Information'); ?>

    <div class="field">
    <?php
        echo form::label('address[address]', 'Address:');
        echo form::input('address[address]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('address[city]', 'City:');
        echo form::input('address[city]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('address[state]', 'State:');
        echo form::dropdown('address[state]', $states);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('address[zip]', 'Zip:');
        echo form::input('address[zip]');
    ?>
    </div>

<?php echo form::close_section(); ?>