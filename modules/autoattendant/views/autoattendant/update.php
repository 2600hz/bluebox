<div id="auto_attendant_update_header" class="update auto_attendant module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="auto_attendant_add_form" class="txt-left form auto_attendant add">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Auto Attendant Information'); ?>

        <div class="field">
            <?php echo form::label('autoattendant[name]', 'Name:'); ?>
            <?php echo form::input(array('name' => 'autoattendant[name]')); ?>
        </div>

        <div class="field">
            <?php echo form::label('autoattendant[description]', 'Description:'); ?>
            <?php echo form::input(array('name' => 'autoattendant[description]')); ?>
        </div>

    <?php echo form::close_section(); ?>

    
    <?php echo form::open_section('Assign Auto Attendant Number(s)'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label(array(
                'for' => '_numbers[assign][]',
                'hint' => 'Numbers that ring this destination directly',
                'help' => 'Select which numbers, in which contexts, will ring this destination directly when they are called. This is a shortcut way of mapping numbers to destinations (versus using the number manager)'
            ),'Select Number:');
            echo numbering::dropdown('AutoAttendantNumber', $autoattendant['auto_attendant_id']);
            echo numbering::nextAvaliableLink('assignAutoAttendantNumber', 'Next Avaliable Number');
        ?>
        </div>

    <?php echo form::close_fieldset(); ?>
    

    <?php echo form::open_section('Prompt'); ?>

        <div class="field">
            <div id="prompt_type">
                <?php echo form::label('autoattendant[type]', 'Prompt type:'); ?>
                <?php echo form::dropdown(array('name' => 'autoattendant[type]', 'class' => 'type'), $promptArray); ?>
            </div>
        </div>

        <div class="field">
            <div id="audio_prompt" class="prompt">
                <?php echo form::label('autoattendant[file_id]', 'Select a file');?>
                <!-- File dropdown widget -->
                <?php echo FileManager::dropdown('autoattendant[file_id]', $autoattendant['file_id'], '', array('audio'));?>
                <?php echo html::anchor('mediamanager/add', 'Upload a new recording', array('class' => 'button'));?>
            </div>
        </div>

        <div class="field">
            <div id="tts_prompt" class="prompt">
                <?php echo form::label('autoattendant[tts_string]', 'Text to Speech Dialog:');?>
                <?php echo form::input('autoattendant[tts_string]');?>
            </div>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('Event Handling'); ?>

        <!--<div class="field">
            <?php echo form::label('autoattendant[dialextension]', 'Allow Extension Dialing?'); ?>
            <?php echo form::checkbox('autoattendant[dialextension]'); ?>
        </div>-->

        <div class="field">
            <?php echo form::label('autoattendant[timeout]', 'No Entry Timeout:'); ?>
            <?php echo form::input('autoattendant[timeout]'); ?> seconds
        </div>

        <div class="field">
            <?php echo form::label('autoattendant[timeout]', 'Inter-Digit Timeout:'); ?>
            <?php echo form::input('autoattendant[digit_timeout]'); ?> seconds
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('Key Mapping'); ?>

        <div id="auto_attendant_table" class="auto_attendant_keymap field">
            
            <?php $iteration = 0; foreach($keys as $digits => $number_id): $iteration++; ?>

                <div id="key_<?php echo $iteration; ?>" class="key">
                    Option <?php echo form::input('keys[' .$iteration .'][digits]', $digits); ?> transfers to a

                    <?php
                        $selectedClass = numbering::getAssignedPoolByNumber($number_id);
                        echo numbering::poolsDropdown('key_' .$iteration .'_class_type', $selectedClass);
                        echo 'named ';
                        echo numbering::numbersDropdown(array(
                            'id' => 'key_' .$iteration .'_number',
                            'name' => 'keys[' .$iteration .'][number_id]',
                            'useNames' => TRUE,
                            'optGroups' => FALSE,
                            //'contextAware' => TRUE
                        ), $number_id);
                        jquery::addQuery('#key_' .$iteration .'_number')->dependent('{ parent: \'key_' .$iteration .'_class_type\', group: \'common_class\' }');
                    ?>

                    <span class="remove_key"></span>

                </div>

            <?php endforeach; ?>

            <?php jquery::addQuery('#auto_attendant_table .remove_key')->click('function (e) { $(this).parent().slideUp(\'500\', function () { $(this).remove() });}'); ?>

        </div>

        <div class="new_option_container">
            <?php echo '<a href="' . url::current() .'" id="new_option" class="nxt_aval_link"><span>New Attendant Option</span></a>'; ?>
        </div>

        <div id="key_template" class="key hide">
            Option <?php echo form::input('key_digits'); ?> transfers to a

            <?php
                echo numbering::poolsDropdown('key_class_type');
                echo 'named ';
                echo numbering::numbersDropdown(array(
                    'id' => 'key_number',
                    'name' => 'keys[]',
                    'useNames' => TRUE,
                    'optGroups' => FALSE,
                    //'contextAware' => TRUE
                ));
            ?>

            <span class="remove_key"></span>
        </div>
        
        <?php javascript::codeBlock(); ?>
            var divCount = $('.auto_attendant_keymap > div').length;

            $('#new_option').click(function (e) {
                e.preventDefault();
                newKey = $('#key_template').clone().appendTo('#auto_attendant_table');

                divCount++;
                
                newKey.attr('id', 'key_' + divCount);
                newKey.find('#key_class_type').attr('id', 'key_' + divCount + '_class_type');
                newKey.find('#key_number').attr('id', 'key_' + divCount + '_number').attr('name', 'keys[' + divCount + '][number_id]');
                newKey.find('#key_digits').attr('id', 'key_' + divCount + '_digits').attr('name', 'keys[' + divCount + '][digits]');

                $('#key_' + divCount + '_number').dependent({ parent: 'key_' + divCount + '_class_type', group: 'common_class'});

                newKey.find('.remove_key').click(function (e) {
                    $(this).parent().slideUp('500', function () { $(this).remove() });
                });

                newKey.slideDown();
            });
        <?php javascript::blockEnd(); ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>

<?php
    jquery::addQuery('.type')->bind('change', 'function(){
        $(\'.prompt\').hide();
        type = $(\'#autoattendant_type\').val();
        if (type != \'\') {
            $(\'#\' + type + \'_prompt\').slideDown();
        }
    }')->trigger('change');
    
    jquery::addPlugin('dependent');
?>