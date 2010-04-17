<div id="numbermanager_update_header" class="update numbermanager module_header">
    <h2><?php echo __($title); ?></h2>
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
        <?php
            echo form::label('number[destination]', 'Destination:');
            echo '<span class="destination_1">';

            if ($assigned = numbering::getDestinationByNumber($number['number_id'])) {
                 if (get_parent_class($assigned) == 'FreePbx_Record') {
                     echo ucfirst(get_class($assigned));
                 } else {
                     echo ucfirst(get_parent_class($assigned));
                 }

                 echo ' ' .implode(', ', $assigned->identifier());

                 if (!empty($assigned['name'])) {
                    echo ' (' .$assigned['name'] .')';
                 }

            } else {
                echo '&nbsp;';
            }

            echo ' </span>';
            echo ' <a href="" id="destination_1" onClick="return false;" class="destination_select" numberId="' . $number['number_id'] . '">Change Destination</a>';

            //echo form::button('number[destination]', 'Change', 'onClick="destinations.select(\'number[destination]\');return false;"');
            

            /*echo numbering::poolsDropdown('number[class_type]');
            echo numbering::destinationsDropdown(array(
                'name' => 'number[foreign_id]',
                'optGroups' => FALSE,
                'nullOption' => FALSE
            ));*/
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Number Pools'); ?>
    
        <p>
            <?php echo __('Number pools allow you to keep similar types of numbers grouped together. For example, you can block out 2XXX for Devices, 30XX for Auto-Attendants, 31XX for Ring Groups, etc. Check the boxes below to specify what types of features can be assigned to this number.'); ?>
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
            <?php echo __('Select the groups of callers who can see/dial/call this number'); ?>
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
