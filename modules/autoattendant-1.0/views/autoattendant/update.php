<div id="auto_attendant_update_header" class="update auto_attendant module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="auto_attendant_add_form" class="txt-left form auto_attendant add">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Auto Attendant Information'); ?>

        <div class="field">
            <?php echo form::label('autoattendant[name]', 'Name:'); ?>
            <?php echo form::input('autoattendant[name]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Prompt'); ?>

        <div class="field">

            <div id="prompt_type">
                <?php echo form::label('autoattendant[registry][type]', 'Prompt type:'); ?>
                <?php 
                    echo form::dropdown(array(
                            'name' => 'autoattendant[registry][type]',
                            'class' => 'type'
                        ), array(
                            'audio' => 'Audio Recording',
                            'tts' => 'Text to Speech'
                        )
                    );
                ?>
            </div>

        </div>

        <div class="field">

            <div id="audio_prompt" class="prompt">
                <?php echo form::label('autoattendant[registry][mediafile_id]', 'Select a file');?>
                <?php echo form::dropdown('autoattendant[registry][mediafile_id]', Media::files()) ;?>
                <?php //echo html::anchor('globalmedia/add', 'Upload a new recording', array('class' => 'button qtipAjaxForm'));?>
            </div>

        </div>

        <div class="field">

            <div id="tts_prompt" class="prompt">
                <?php echo form::label('autoattendant[registry][tts_string]', 'Text to Speech Dialog:');?>
                <?php echo form::textarea('autoattendant[registry][tts_string]');?>
            </div>

        </div>
     
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('General Behavior'); ?>

        <div class="field">
            <?php echo form::label('autoattendant[digit_timeout]', 'Inter-Digit Timeout:'); ?>
            <?php echo form::input('autoattendant[digit_timeout]'); ?>
            <?php //javascript::codeBlock('$("#autoattendant_digit_timeout").spinner({max: 10, min: 1});'); ?>
        </div>

        <div class="field">
            <?php echo form::label('autoattendant[timeout]', 'No Entry Timeout:'); ?>
            <?php echo form::input('autoattendant[timeout]'); ?>
            <?php //javascript::codeBlock('$("#autoattendant_timeout").spinner({max: 10, min: 1});'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Dial by Extension'); ?>

        <div class="field">
            <?php
                echo form::label(array(
                        'for' => 'autoattendant[extension_context_id]',
                        'hint' => 'Numbers that callers can directly dial into',
                    ),
                    'Internal Extension Context:'
                );
            ?>
            <?php
                echo numbering::selectContext(array(
                        'name' => 'autoattendant[extension_context_id]',
                        'nullOption' => 'Disabled'
                    )
                );
            ?>
        </div>

        <div class="field">
            <?php echo form::label('autoattendant[extension_digits]', 'Maximum Extension Length:'); ?>
            <?php echo form::input('autoattendant[extension_digits]'); ?>
            <?php //javascript::codeBlock('$("#autoattendant_extension_digits").spinner({max: 9, min: 3});'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::open_section('Key Mapping'); ?>

        <div id="auto_attendant_keymap">&nbsp;</div>

        <div class="new_option_container">
            <?php echo '<a href="' . url::current() .'" id="new_option" class="nxt_aval_link"><span>New Attendant Option</span></a>'; ?>
        </div>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>
</div>

<?php jquery::addPlugin(array('dependent', 'spinner')); ?>

<?php javascript::codeBlock(NULL, array('scriptname' => 'update_autoattendant')); ?>

    $('.type').bind('change', function () {

        $('.prompt').hide();

        var type = $('#autoattendant_registry_type').val();

        if (type != '') {

            $('#' + type + '_prompt').slideDown();

        }

    }).trigger('change');

    var key_template = '<?php echo str_replace(array("\n", '  '), '', new View('autoattendant/keytemplate')); ?>';

    var numbering_json = <?php echo $numberingJson; ?>;

    var keys = <?php echo $keys; ?>;

    numbering_json['key_number'] = 0;

    $.each(keys, function(index, value){

        numbering_json['key_number'] = index;

        $('#auto_attendant_keymap').append(Mustache.to_html(key_template, $.extend(value, numbering_json)));

        selectedDestination = $('#key_' + index + '_number option[value=' + value.number_id + ']');

        selectedDestination.attr('selected', 'selected');

        $('#key_' + index + '_class_type option[title=' + selectedDestination.attr('class') + ']').attr('selected', 'selected');

        $('#key_' + index + '_number').dependent({ parent: 'key_' + index + '_class_type', group: 'common_class' });

        $('#auto_attendant_keymap #remove_key_' + index).click(function (e) {

            e.preventDefault();

            $(this).parent().slideUp('500', function () {

                $(this).remove()

            });

        });

    });

    $('.new_option_container #new_option').click(function (e) {

        e.preventDefault();

        numbering_json['key_number'] += 1;

        $('#auto_attendant_keymap').append(Mustache.to_html(key_template, numbering_json));

        $('#key_' + numbering_json['key_number'] + '_number').dependent({ parent: 'key_' + numbering_json['key_number'] + '_class_type', group: 'common_class' });

        $('#auto_attendant_keymap #remove_key_' + numbering_json['key_number']).click(function (e) {

            e.preventDefault();

            $(this).parent().slideUp('500', function () {

                $(this).remove()

            });

        });

    });

<?php javascript::blockEnd(); ?>