<div id="numbermanager_update_header" class="update numbermanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="numbermanager_update_form" class="update numbermanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Manage'); ?>

        <div class="field">
        <?php
            echo form::label('number[number]', 'Number:');
            echo form::input('number[number]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number[location_id]', 'Location:');
            echo locations::dropdown('number[location_id]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views, array('number_targets', 'terminate_options', 'number_pools', 'number_contexts'));
        }
    ?>

    <?php if (!empty($create_number_type)): ?>

        <fieldset class="hidden_inputs">

            <input type="hidden" class=" hidden" value="<?php echo $create_class_type; ?>" name="create_class_type">

            <input type="hidden" class=" hidden" value="<?php echo $create_number_type; ?>" name="number[NumberPool][][number_type_id]">

        </fieldset>
            
    <?php endif; ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>
        
</div>