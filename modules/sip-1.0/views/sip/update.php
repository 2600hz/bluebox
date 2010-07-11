<?php echo form::open_section('SIP Device Settings'); ?>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'sip[username]',
                'hint' => 'Username used when registering',
                'help' => 'SIP usernames are used when a SIP device is registering to a server. Ensure this is a unique username. In some software and hardware devices you will need to append @host.name.com to the end of the SIP username in order for registration to work.<BR><BR>NOTE: If you leave this blank, it is assumed that registration is not required.'
            ),
            'SIP Username:'
        );
        echo form::input('sip[username]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'sip[password]',
                'hint' => 'Password used when registering',
                'help' => 'SIP passwords are used when a SIP device is registering to a server. This is SIPs primary protection mechanism so you should use passwords that are hard to guess!'
            ),
            'SIP Password:'
        );
        echo form::input(array('name' => 'sip[password]', 'class' => 'password_entry'));
    ?>
    </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sip[cid_format]',
                    'hint' => 'Caller ID format on inbound calls',
                    'help' => 'The system can automatically reformat Caller ID in E.164 form or other varying forms so that it appears more standardized on your phone\'s Caller ID display. Choose the format you wish to appear.'
                ),
                'Caller ID Format'
            );
            echo sip::dropdownCIDFormat('sip[cid_format]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sip[sip_invite_format]',
                    'hint' => 'Invite format on calls to this device',
                    'help' => 'Some phones, switches and other devices will only accept SIP INVITE requests if they are in a specific format, such as E.164 (+1NPANXXXXXX, etc.) or 10-digit form. This selection decides how to preformat the INVITEs that go to this device.'
                ),
                'SIP Invite Format'
            );
            echo sip::dropdownInviteFormat('sip[sip_invite_format]');
        ?>
        </div>

<?php echo form::close_section(); ?>