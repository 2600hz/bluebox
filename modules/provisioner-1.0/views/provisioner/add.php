<div id="provisioner_add_header" class="add provisioner module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="provisioner_add_form" class="update provisioner">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Identification'); ?>

        <div class="field">
        <?php
            echo form::label('endpoint[mac]', 'MAC Address:');
            echo form::input('endpoint[mac]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('endpoint[endpoint_model_id]', 'Type');
            echo form::dropdown(array('name' => 'endpoint[endpoint_vendor_id]', 'id' => 'endpoint_vendor_id'), $vendors);
            echo form::dropdown(array('name' => 'endpoint[endpoint_model_id]', 'id' => 'endpoint_model_id'),  $models);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'cancel'
				      ,'class' => 'cancel small_red_button'
				      ,'onclick' => 'window.location="' . url::site('provisioner/index') . '";return false;'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>

<?php    
    jquery::addPlugin('blockUI');
    jquery::addPlugin('selectbox');
    jquery::addQuery('#endpoint_vendor_id')->change('function () {
        $.blockUI({ message: \'<h1>' .__('Please Wait...') .'</h1>\' });
        modelDrop = $(\'#endpoint_model_id\');
        modelDrop.removeOption(/./);
        modelDrop.ajaxAddOption(\'' .url::site('provisioner/get') .'\', {\'id\' : $(this).val(), \'type\' : \'models\'}, false);
    }');
    jquery::addQuery('')->ajaxStop('$.unblockUI');
    //selectedValues()
