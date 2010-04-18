<div id="provisioner_edit_header" class="edit provisioner module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="provisioner_edit_form" class="edit provisioner">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Identification'); ?>

        <div class="field">
        <?php
            echo form::label('endpoint[mac]', 'MAC Address:');
            echo form::input(array('name' => 'endpoint[mac]', 'readonly' => 'readonly'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('endpoint[endpoint_model_id]', 'Type');
            echo form::input(array('name' => 'endpoint[endpoint_vendor]', 'readonly' => 'readonly'), $vendor);
            echo form::input(array('name' => 'endpoint[endpoint_model]', 'readonly' => 'readonly'), $model);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>