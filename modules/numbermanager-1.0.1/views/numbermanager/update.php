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


        <div class="field">
            <?php echo form::label('number[type]', 'Type:'); ?>
        </div>

        <div class="fields">
            <?php echo form::label('number_type_int', 'Internal'); ?>
            <?php echo form::radio(array('name' => 'number[type]', 'id' => 'number_type_int'), Number::TYPE_INTERNAL); ?>

            <?php echo form::label('number_type_ext', 'External'); ?>
            <?php echo form::radio(array('name' => 'number[type]', 'id' => 'number_type_ext'), Number::TYPE_EXTERNAL); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views, array('number_targets', 'terminate_options', 'number_contexts', 'number_pools'));
        }
    ?>

    <?php if (!empty($create_number_type)): ?>

        <fieldset class="hidden_inputs">

            <input type="hidden" class=" hidden" value="<?php echo $create_class_type; ?>" name="create_class_type">

            <input type="hidden" class=" hidden" value="<?php echo $create_number_type; ?>" name="number[NumberPool][][number_type_id]">

        </fieldset>
            
    <?php endif; ?>

    <?php echo form::close(TRUE); ?>
        
</div>

<?php javascript::codeBlock(); ?>

    $('input[name="number[type]"]').change(function(){ 

        if($('input[name="number[type]"]:checked').val() == 1)
        {
            $('.number_context_option').each( function(i, e) {
                var radio = $(e).clone().attr('type', 'radio');

                $(e).parent().append(radio);

                $(e).remove();
            });
        }
        else
        {
            $('.number_context_option').each( function(i, e) {
                var checkbox = $(e).clone().attr('type', 'checkbox');

                $(e).parent().append(checkbox);

                $(e).remove();
            });
        }
    }).trigger('change');

<?php javascript::blockEnd(); ?>