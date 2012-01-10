<!-- start here, see phpdoc style comments in  bb/libraries/drivers/telelphony.php -->

<div id="feature_code_update_header" class="update feature_code module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="feature_code_add_form" class="txt-left form feature_code add">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Feature Code Details'); ?>

        <div class="field">
            <?php echo form::label('featurecode[name]', 'Name:'); ?>
            <?php echo form::input('featurecode[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[description]', 'Description:'); ?>
            <?php echo form::input('featurecode[description]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[registry][feature]', 'Feature:'); ?>
            <?php echo form::dropdown('featurecode[registry][feature]',
                    array(//'forward_on' => 'Call-Forward Activate',
                          //'forward_off' => 'Call-Forward Disable',
                          'ivr_return' => 'Return to last AutoAttendant',
                          'redial' => 'Redial',
                          'call_return' => 'Call Return',
                          'voicemail' => 'Check Voicemail',
                          'voicemail_quickauth' => 'Check Voicemail (Mailbox = Device MWI)',
                          'voicemail_noauth' => 'Check Voicemail (No Authorization, Mailbox = Device MWI)',
                          'park' => 'Park',
                          'unpark' => 'Unpark / Pickup',
                          'parking_void' => 'Parking void (Park into/Unpark any parking lot)',
                          'echo' => 'Echo Test',
                          'delay_echo' => 'Delayed Echo Test',
                          'tone_test' => 'Miliwatt Tone Test',
                          'hold_music' => 'Hold Music Test',
                          'eavesdrop' => 'Eavesdrop',
                          'uuid_standby' => 'Call Center - UUID Standby',
                          'agent_login' => 'Call Center - Agent Login',
                          'agent_logout' => 'Call Center - Agent Logout'));

            ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

</div>
