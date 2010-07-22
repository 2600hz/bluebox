<div id="conferences_update_header" class="update conferenece module_header">

    <h2><?php echo $title; ?></h2>

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
            echo form::label(array(
                    'for' => 'conference[pins][0]',
                    'hint' => 'Leave blank for no pin'
                ),
                'Pin:'
            );
            echo form::input('conference[pins][0]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Conference Features'); ?>

        <div class="field">
        <?php
            echo form::label('conference[registry][record]', 'Record conference?');
            echo form::checkbox('conference[registry][record]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('conference[registry][moh_type]', 'Pre-Conference Music');
            echo form::dropdown('conference[registry][moh_type]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (FALSE): ?>

        <?php echo form::open_section('Music and Audio'); ?>

            <div class="field">
            <?php
                echo form::label('conference[registry][record_location]', 'Recorded File Location:');
                echo form::input('conference[registry][record_location]');
            ?>
            </div>

            <div class="field">
            <?php
                echo form::label('conference[registry][conference_soundmap_id]', 'Event Sound Map');
                echo form::dropdown('conference[registry][conference_soundmap_id]', array(
                    1 => 'Default'
                ));
            ?>
            </div>

            <div class="field">
            <?php
                echo form::label('conference[registry][comfort_noise]', 'Generate Comfort Noise?');
                echo form::checkbox('conference[registry][comfort_noise]');
            ?>
            </div>

        <?php echo form::close_section(); ?>
    
    <?php endif; ?>
    
    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>
            
    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>
    
</div>


