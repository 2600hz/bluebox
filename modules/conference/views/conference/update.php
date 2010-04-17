<div id="conferences_update_header" class="update conferenece module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="conferences_update_form" class="update conferenece">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Conference Information'); ?>

        <div class="field">
        <?php
            echo form::label('conference[name]', 'Conference Name:');
            echo form::input('conference[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('conference[room_pin]', 'Conference Room Pin:');
            echo form::input('conference[room_pin]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Assign Conference Number(s)'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label(array(
                'for' => '_numbers[assigned][]',
                'hint' => 'Numbers that ring this destination directly',
                'help' => 'Select which numbers, in which contexts, will ring this destination directly when they are called. This is a shortcut way of mapping numbers to destinations (versus using the number manager)'
            ),'Select Number:');
            echo numbering::dropdown('ConferenceNumber', $conference['conference_id']);
            echo numbering::nextAvaliableLink('assignConferenceNumber', 'Next Avaliable Number');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Conference Features'); ?>

        <div class="field">
        <?php
            echo form::label('conference[record]', 'Record conference?');
            echo form::checkbox('conference[record]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('conference[record_location]', 'Recorded File Location:');
            echo form::input('conference[record_location]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Music and Audio'); ?>

        <div class="field">
        <?php
            echo form::label('conference[conference_soundmap_id]', 'Event Sound Map');
            echo form::dropdown('conference[conference_soundmap_id]', array(
                1 => 'Default'
            ));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('conference[comfort_noise]', 'Generate Comfort Noise?');
            echo form::checkbox('conference[comfort_noise]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('conference[moh_type]', 'Pre-Conference Music');
            echo form::dropdown('conference[moh_type]', $mohTypeOptions);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <?php if (isset($grid)) : ?>
        <?php echo form::open_section('Pin List'); ?>

            <p>
                <?php echo __('Use this list to set room, moderator and/or per-member pins and associate available key map options to each pin. Key maps specify what buttons callers can push to access various conference features.'); ?>
            </p>

            <?php echo $grid; ?>

        <?php form::close_section(); ?>
        
    <?php endif; ?>
            
    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>


