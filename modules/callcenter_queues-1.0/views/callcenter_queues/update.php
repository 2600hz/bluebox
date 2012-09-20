<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div id="callcenter_update_header" class="txt-center update callcenter module_header">
    <h2><?php echo $mode=='create'?'Create':'Edit' ?> Queue</h2>
</div>

<div id="callcenter_update_form" class="update callcenter">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Queue'); ?>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_name]',
                    'hint' => 'Queue Name',
                    'help' => 'Meaningfull name of queue.  It will be combined with the location to have the form name@domain'
                ),
                 'Name:'
            );
            echo form::input('callcenter_queue[ccq_name]', null, ($mode == 'edit' ? 'onFocus="this.blur()"' : ''));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
                 'for' => 'callcenter_queue[ccq_locationid]'),
                 'Location:'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_locationid]'), $locations, null, ($mode == 'edit' ? 'onFocus="this.blur()"' : ''));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
			'for' => 'callcenter_queue[ccq_moh_type]',
			'hint' => 'Pre conference sound',
			'help' => 'Sound (or silence) to play while on hold in the queue.  Items marked not recommended are because they cause the system to play the source seprerately for each person in the queue, consuming resources.<br><br>Music on Hold Local Stream - Plays the default Music on Hold Stream<br>Other Local Steam - Specify the name of the stream in \'MOH Information\'<br>Default Ringing - Plays the file specified in ${us-ring} in a loop<br>TTS - Speaks the phrase specified in \'MOH Information\'<br>Default Music on Hold (Not Recommended) - Plays the sound file specified in ${hold_music} in a loop.<br>Specific File (Not Recommended) - Full path to a file to play in a loop<br>Silence - No sound while waiting in the queue'
		),
		'MOH:'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_moh_type]'), $mohoptions);
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_moh_data]',
                    'hint' => 'MOH Information',
                    'help' => 'If you select the following MOH option, enter:<br>Other Local Stream - Name of Stream<br>TTS - Phrase to speak<br>Specific File - Full path to sound file'
                ),
                'MOH Information:'
            );
            echo form::input(array('name'=>'callcenter_agent[ccq_moh_data]', 'size' => 90));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[ccq_record_template]',
                    'help' => 'Use the record-template to save your recording wherever you would like on the filesystem. It\'s not uncommon for this setting to start with \'$${base_dir}/recordings/\'. Whatever directory you choose, make sure it already exists and that the softswitch has the required permissions to write to it.'
                ),
                'Recording Template:'
            );
            echo form::input(array('name'=>'callcenter_agent[ccq_record_template]', 'size' => 90));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
			'for' => 'callcenter_queue[ccq_time_based_score]',
			'hint' => 'System or Queue based call scoring',
			'help' => 'If set to system, it will add the number of seconds since the call was originally answered (or entered the system) to the caller\'s base score. Raising the caller\'s score allows them to receive priority over other calls that might have been in the queue longer but not in the system as long. If set to queue, you get the default behavior, i.e., nobody\'s score gets increased upon entering the queue (regardless of the total length of their call).'
		),
		'Call Scoring:'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_time_based_score]'), $tbsoptions);
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
			'for' => 'callcenter_queue[ccq_tier_rule_apply]',
			'hint' => 'Apply tiers to agents or treat all agents equally',
			'help' => 'This defines if we should apply the following tier rules when a caller advances through a queue\'s tiers. If No, they will use all tiers with no wait.'
		),
                 'Use Tiering Rules?'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_tier_rule_apply]'), array('true' => 'Use tiers', 'false' => 'Do not use tiers'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_tier_rule_wait_time]',
                    'hint' => 'Required wait time per tier',
                    'help' => 'The time in seconds that a caller is required to wait before advancing to the next tier. This will be multiplied by the tier level if tier-rule-wait-multiply-level is Yes.'
                ),
                'Tier Wait Time:'
            );
            echo form::input(array('name'=>'callcenter_queue[ccq_tier_rule_wait_time]'));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
			'for' => 'callcenter_queue[ccq_tier_rule_wait_multipy_level]',
			'hint' => 'Multiply required wait time by tier',
			'help' => 'If No, then once tier-rule-wait-second is passed, then the caller will advance through the tiers in order. If Yes, the tier-rule-wait-second will be multiplied by the tier level and the caller will have to wait longer before advancing to the next tier.'
		),
                'Multiply Wait Time?'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_tier_rule_wait_multipy_level]'), array('true' => 'Yes', 'false' => 'No'));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
                 'for' => 'callcenter_queue[ccq_tier_rule_noagent_nowait]',
                    'hint' => 'Skip tiers with no logged in agents',
                    'help' => 'If Yes, callers will skip tiers that don\'t have agents available. Otherwise, they are be required to wait before advancing. Agents must be logged off to be considered not available.'
		),
                'Skip empty tiers?'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_tier_rule_noagent_nowait]'), array('true' => 'Yes', 'false' => 'No'));
        ?>
        </div>

	<div class="field">
        <?php
            echo form::label(array(
			'for' => 'callcenter_queue[ccq_abandoned_resume_allowed]',
			'hint' => 'Get back in same spot in line',
			'help' => 'If Yes, a caller who has abandoned the queue can re-enter and resume their previous position in that queue. In order to maintain their position in the queue, they must not abandoned it for longer than the number of seconds defined in \'Abandond discard delay:\'.'
		),
                'Allow resume queue position?'
            );
            echo form::dropdown(array('name'=>'callcenter_queue[ccq_abandoned_resume_allowed]'), array('true' => 'Yes', 'false' => 'No'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_discard_abandonded_after]',
                    'hint' => 'Seconds before abandoned call is discarded',
                    'help' => 'The number of seconds before we completely remove an abandoned member from the queue. When used in conjunction with \'Allow resume queue position\', callers can come back into a queue and resume their previous position.'
                ),
                'Abandoned discard delay:'
            );
            echo form::input(array('name'=>'callcenter_queue[ccq_discard_abandonded_after]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_max_wait_time]',
                    'hint' => 'Max queue wait time without agent assigned',
                    'help' => 'Seconds a call will wait in a queue before it exits the queue. This allows the call to exit the queue if no agents are available, but only after a certain time. This can protect kicking all member waiting if all agents are logged off by accident or allow you to offer a \'Leave a voice mail\' option. 0 = disabled'
                ),
                'Max Wait Time:'
            );
            echo form::input(array('name'=>'callcenter_queue[ccq_max_wait_time]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_max_wait_time_no_agent]',
                    'hint' => 'Max queue wait time with no available agents',
                    'help' => 'Seconds a call will wait in a queue empty of available agents (on a call or not) before it exits the queue. This allows the call to exit the queue if no agents are available, but only after a certain time. This can protect kicking all member waiting if all agents are logged off by accident or allow you to offer a \'Leave a voice mail\' option. 0 = disabled'
                ),
                'No Agent Wait Time:'
            );
            echo form::input(array('name'=>'callcenter_queue[ccq_max_wait_time_no_agent]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_queue[ccq_max_wait_time_with_no_agent_time_reached]',
                    'hint' => 'Remaining call wait time without assignment',
                    'help' => 'Seconds subsequent calls will be denied entry into the queue and for other calls in the queue to wait to be assigned to an agent before exiting the queueafter the \'No Agent Wait Time\' is reached .'
                ),
                'Remaining Wait Time:'
            );
            echo form::input(array('name'=>'callcenter_queue[ccq_max_wait_time_with_no_agent_time_reached]'));
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