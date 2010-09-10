<div id="endpointmanager_update_header" class="update endpointmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="endpointmanager_update_form" class="txt-left form endpointmanager update">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Endpoint Device'); ?>

        <div class="field">
            <?php echo form::label('endpointdevice[description]', 'Description:'); ?>
            <?php echo form::input('endpointdevice[description]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('endpointdevice[mac]', 'MAC Address:'); ?>
            <?php echo form::input('endpointdevice[mac]'); ?>
        </div>

        <div class="field">
        <?php
            echo form::label('endpointmanager[endpoint_model_id]', 'Model:');
            echo form::dropdown('endpointmanager[endpoint_model_id]', array('1' => '6755i'));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>