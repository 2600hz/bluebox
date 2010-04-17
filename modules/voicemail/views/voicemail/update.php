<div id="voicemail_update_header" class="txt-center update voicemail module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="voicemail_update_form" class="update voicemail">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Voicemail Box'); ?>

        <div class="field">
        <?php
            echo form::label(array('for' => 'voicemail[name]',
                                   'hint' => 'Voicemail Box Name',
                                   'help' => 'A nickname or friendly name for this voicemail box, such as \'Support Mailbox\'.'),
                             'Mailbox Name:');
            echo form::input('voicemail[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'voicemail[mailbox]',
                                   'hint' => 'Voicemail Box User ID',
                                   'help' => 'Voicemail box number to utilize for voicemail. This does not have to match a user\'s extension, a device username, or anything else. More then one user and/or device can share the same mailbox.'),
                             'Mailbox #:');
            echo form::input('voicemail[mailbox]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('voicemail[password]', 'Password:');
            echo form::input('voicemail[password]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Assign Device Number(s)'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label(array(
                'for' => '_numbers[assigned][]',
                'hint' => 'Optional numbers that ring this directly',
                'help' => 'Select which numbers, in which contexts, will ring this destination directly when they are called. This is a shortcut way of mapping numbers to destinations (versus using the number manager)'
            ),'Select Number:');
            echo numbering::dropdown('VoicemailNumber', $voicemail['voicemail_id']);
            echo numbering::nextAvaliableLink('assignVoicemailNumber', 'Next Avaliable Number');
        ?>
        </div>
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Voicemail To Email'); ?>

        <div class="field">
        <?php
            echo form::label('voicemail[email_all_messages]', 'Email All Messages');
            echo form::checkbox(array('class' => 'determinant agent_for_emailVM',  'name' => 'voicemail[email_all_messages]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('voicemail[email_address]', 'Email Address:');
            echo form::input(array('class' => 'dependent_positive rely_on_emailVM',  'name' => 'voicemail[email_address]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('voicemail[delete_file]', 'Delete Message After Emailing');
            echo form::checkbox(array('class' => 'dependent_positive rely_on_emailVM',  'name' => 'voicemail[delete_file]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('voicemail[attach_audio_file]', 'Attach Audio to Email');
            echo form::checkbox(array('class' => 'dependent_positive rely_on_emailVM', 'name' => 'voicemail[attach_audio_file]'));
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