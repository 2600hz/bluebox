<?php echo form::open_section('SIP Settings'); ?>

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

    <?php if (isset($trunk_options)): ?>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'sip[from_domain]',
                'hint' => 'Force a domain name in From: field',
                'help' => 'Sometimes providers require explicit domain names in the From: field for your calls to work (and bill) properly. This option, if set, will force a from domain on all your calls in the domain part of the SIP From: field'
            ),
            'SIP From Domain (optional):'
        );
        echo form::input('sip[from_domain]');
    ?>
    </div>

    <?php endif; ?>

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
                    'for' => 'sip[caller_id_field]',
                    'hint' => 'Which header field to use',
                    'help' => 'Different providers expect Caller ID info in different fields. This option lets you pick which field to put the Caller ID info into. If you are finding that Caller ID does not work on outbound calls, try changing this field until it does work. NOTE: If you change this to the From: field and you are using a provider who requires registration with a username and password, this option will likely break all outbound calls.'
                ),
                'Outbound Caller ID Field'
            );
            echo form::dropdown('sip[caller_id_field]', array('rpid' => 'Remote-Party-Id', 'pid' => 'P-Asserted-Identity', 'from' => 'From: Field'));
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

    <?php if (isset($trunk_options)): ?>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sip[to_user]',
                    'hint' => 'Use custom to_user header for DID detection',
                    'help' => 'Some providers send calls to the username you registered as and provide the DID that is being called via an alternate header. If you find that calls from outside providers are not reaching your system with the correct phone number, try enabling this setting.'
                ),
                'Inbound DID in To-User'
            );
            echo form::checkbox('sip[to_user]');
        ?>
        </div>

    <?php endif; ?>


<?php echo form::close_section(); ?>