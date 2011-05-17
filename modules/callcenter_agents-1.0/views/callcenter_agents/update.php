<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callcenter_update_header" class="txt-center update callcenter module_header">
    <h2><?php echo $mode=='create'?'Create':'Edit' ?> Agent</h2>
</div>

<div id="callcenter_update_form" class="update callcenter">
    
    <?php echo form::open(); ?>

    <?php echo form::open_section(''); ?>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_loginid]',
                    'hint' => 'User login (numeric)',
                    'help' => 'ID for agent to log in or change status on system.  It is recommended that this is a numeric value so that actions can easily be taken using the keypad on the phone.  Will be combined with the location to be in the form user@domain'
                ),
                 'Login ID:'
            );
            echo form::input('callcenter_agent[cca_loginid]', null, ($mode == 'edit' ? 'onFocus="this.blur()"' : ''));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                 'for' => 'callcenter_agent[cca_locationid]'),
                 'Location:'
            );
            echo form::dropdown(array('name'=>'callcenter_agent[cca_locationid]'), $locations, null, ($mode == 'edit' ? 'onFocus="this.blur()"' : ''));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_displayname]',
                    'hint' => 'Name to Display',
                    'help' => 'Name to display within Bluebox Call Center Applications for this agent.'
                ),
                'Name:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_displayname]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_type]',
                    'hint' => 'Agent call receipt strategy',
                    'help' => 'Callback will try to reach the agent via the contact fields value.<br>UUID-Standby will try to directly bridge the call using the agent uuid.'
                ),
                'Type:'
            );
            echo form::dropdown(array('name'=>'callcenter_agent[cca_type]'), $agent_types);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_contact]',
                    'hint' => 'Dial String',
                    'help' => 'Dial string that will be used to contact agent. For example, the dial string \'[call_timeout=10]user/1000@default\' will call extension 1000 in the default domain and the call will fail after 10 seconds if it is not answered.'
                ),
                'Contact:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_contact]', 'size' => 90));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_status]',
                    'hint' => 'Status at reload',
                    'help' => 'Logged Out - Cannot receive queue calls.<br>Available - Ready to receive queue calls.<br>Available (On Demand) - State will be set to \'Idle\' once the call ends (not automatically set to \'Waiting\').<br>On Break - Still Logged in, but will not receive queue calls.'
                ),
                'Status:'
            );
            echo form::dropdown(array('name'=>'callcenter_agent[cca_status]'), $statuses);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_max_no_answer]',
                    'hint' => 'No anwer before auto \'On Break\'',
                    'help' => 'If the agent reach this number of consecutive no answer, his or her status is changed to \'On Break\' automaticly.'
                ),
                'Max No Answer:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_max_no_answer]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_wrap_up_time]',
                    'hint' => 'Delay in between calls',
                    'help' => 'Allow an agent to have a delay when finishing a call before receiving another one.'
                ),
                'Wrap Up Time:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_wrap_up_time]'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_reject_delay_time]',
                    'hint' => 'Delay after reject',
                    'help' => 'If the agent press the reject on their phone, we wait this defined time amount.'
                ),
                'Reject Delay Time:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_reject_delay_time]'));
        ?>
        </div>

         <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_agent[cca_busy_delay_time]',
                    'hint' => 'Delay after DND',
                    'help' => 'If the agent is on do not disturb, we wait this defined time before trying him or her again.'
                ),
                'Busy Delay Time:'
            );
            echo form::input(array('name'=>'callcenter_agent[cca_busy_delay_time]'));
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