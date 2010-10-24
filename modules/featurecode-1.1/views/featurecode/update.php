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
                          'redial' => 'Redial',
                          'call_return' => 'Call Return',
                          'voicemail' => 'Check Voicemail',
                          'voicemail_quickauth' => 'Check Voicemail (Mailbox = Internal Caller ID)',
                          'voicemail_noauth' => 'Check Voicemail (No Authorization, Mailbox = Internal Caller ID)',
                          'park' => 'Park',
                          'unpark' => 'Unpark / Pickup',
                          'echo' => 'Echo Test',
                          'delay_echo' => 'Delayed Echo Test',
                          'tone_test' => 'Miliwatt Tone Test',
                          'hold_music' => 'Hold Music Test'));
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