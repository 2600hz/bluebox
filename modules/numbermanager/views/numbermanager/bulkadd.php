<div id="numbermanager_bulkadd_header" class="bulkadd numbermanager module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="numbermanager_bulkadd_form" class="bulkadd numbermanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Bulk Add'); ?>

        <p>
            <?php echo __('Adding unmapped numbers makes them available for assignment to Bluebox Applications. You can add up to 10,000 numbers at a time.'); ?>
        </p>

        <div class="field">
        <?php
            echo form::label('number[start_number]', 'Start Number:');
            echo form::input('start_number', $start_number);
        ?>
        </div>


        <div class="field">
        <?php
            echo form::label('number[end_number]', 'End Number:');
            echo form::input('end_number', $end_number);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number[location_id]', 'Location:');
            echo locations::dropdown('number[location_id]');
        ?>
        </div>
        
        <div class="field">
        <?php
            echo form::label('number[number_id]', 'Destination:');
            echo numbering::poolsDropdown('number[class_type]');
            echo numbering::destinationsDropdown(array(
                'name' => 'number[foreign_id]',
                'optGroups' => FALSE,
                'nullOption' => FALSE
            ));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Number Pools'); ?>

        <p>
            <?php echo __('To make a number avaliable for a destination to use, you must include the pool that belongs to that destination. By selecting specific number pools, you ensure similar types of numbers stay grouped together (i.e. user extensions are 1XXX, features are 2XXX, ect).'); ?>
        </p>

        <?php
            foreach ($numberTypes as $numberType) {
                echo '<div class="field">';
                echo form::label('numberPools', substr($numberType['class'], 0, strlen($numberType['class']) - 6));
                echo form::checkbox('number[NumberPool][][number_type_id]', $numberType['number_type_id'], isset($checkedNumberTypes[$numberType['number_type_id']]));
                echo '</div>';
            }
        ?>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Contexts'); ?>

        <p>
            <?php echo __('Select what groups are able to see/dial/call this number'); ?>
        </p>

        <?php
            foreach ($contexts as $context) {
                echo '<div class="field">';
                echo form::label('numberContexts', $context['name']);
                echo form::checkbox('number[NumberContext][][context_id]', $context['context_id'], isset($checkedNumberContext[$context['context_id']]));
                echo '</div>';
            }
        ?>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>

<?php
    jquery::addPlugin('dependent');
    jquery::addQuery('#number_foreign_id')->dependent('{ parent: \'number_class_type\', group: \'selectable\' }');
?>