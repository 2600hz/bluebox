<div id="sipinterface_update_header" class="update sipinterface module_header">
    <h2><?php echo $title; ?></h2>
</div>

<div id="sipinterface_update_form" class="update sipinterface">
    <?php echo form::open(); ?>

    <?php echo form::open_section('SIP Interface Information'); ?>

        <div class="field">
        <?php
            echo form::label('sipinterface[name]', 'SIP Interface Friendly Name:');
            echo form::input('sipinterface[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[ip_address]',
                                   'hint' => 'Leave blank to auto-detect',
                                   'help' => 'The IP address you wish to send/receive IP traffic on, for the purposes of this interface. You can define multiple SIP interfaces to allow inbound/outbound calling from multiple IPs on the same machine. On most setups, you only have one IP and can leave this blank to allow auto-detection.<BR><BR>NOTE: If you manually specify an IP address that is not actually on your system, FreeSWITCH will not bind properly to your interface and will fail to send/receive any calls on this IP! Make sure you specify an IP that actually is configured on your local system. (It can be a virtual or VLAN IP)'),
                             'IP Address to Bind To:');
            echo form::input('sipinterface[ip_address]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('sipinterface[port]', 'Port to Bind To:');
            echo form::input('sipinterface[port]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[ext_ip_address]',
                                   'hint' => 'Leave blank to auto-detect',
                                   'help' => 'The external IP address is used in SIP and SDP headers to specify where packets should be routed to/from when talking with external/remote servers. This is very important - incorrectly setting this is very often the cause of one-way audio, since SIP packets will sometimes successfully make it back and forth but the SDP header will have an invalid address for audio.'),
                             'External IP Address:');
            echo form::input('sipinterface[ext_ip_address]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                'for' => 'behind_nat',
                'hint' => 'Detect IP, even when behind NAT',
                'help' => 'Checking this box will allow your FreeSWITCH server to attempt to auto-detect your public IP address for you when you are behind NAT. Various technologies, UPnP, are utilized to try and gather this information.'
                ),
                'Server is behind NAT?'
            );
            echo form::checkbox(array('class' => 'determinant agent_for_natType',  'name' => 'behind_nat'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sipinterface[nat_type]',
                    'hint' => 'How to detect the public IP',
                    'help' => 'FreeSWITCH has multiple ways to check for your public IP when behind NAT. One way is to use a STUN server. This will ask a public server on the internet what your IP address is each time a call is placed. The other option is to use uPnP and related local LAN technologies. This will query your uPnP-enabled firewall for your actual public IP address.<br><br>NOTE: STUN requests can cause pauses when placing phone calls. A STUN lookup is done each time a call is placed. The call pauses while the lookup occurs and the system waits for a response. Use uPnP if possible to avoid this delay.'
                ), 
                'NAT Detection Mechanism: '
            );
            echo form::dropdown(array('class' => 'dependent_positive rely_on_natType', 'name' => 'sipinterface[nat_type]'), array(1 => 'Detect IP via uPnP', 2 => 'Detect IP via STUN Server'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[auth]',
                                   'hint' => 'Require a SIP username/password?',
                                   'help' => 'Whether or not to require a username/password for SIP registrations. Usually you want this checked. Note that ACLs are checked first and if someone matches an ACL it overrides the requirement for a SIP username/password challenge.<BR><BR>WARNING: Turning this option off on a publicly exposed IP is usually considered dangerous unless you are careful with what destinations you make accessible. Everyone on the Internet will be able to use all numbers and features on your system via this IP address and the context you select below if you uncheck this box. This warning only applies to public IP addresses.!'),
                             'Enable Auth/Challenge?');
            echo form::checkbox('sipinterface[auth]');
        ?>
        </div>
    
        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[multiple]',
                                   'help' => 'Allow more than one device to register using the same credintials at a time. When calls are placed to the SIP user all registered phones will ring simultaneously. The first phone to answer gets the call.'
                                   ), 'Allow multiple registrations:');
            echo form::checkbox('sipinterface[multiple]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[registry][compact_headers]',
                                   'help' => 'Use SIP-compliant compact headers. Useful to fix broken UDP, where the packets are exceeding the size the router allows'
                                   ), 'Use Compact Headers:');
            echo form::checkbox('sipinterface[registry][compact_headers]');
        ?>
        </div>

    <?php echo form::close_section(); ?>
    
    <?php echo form::open_section('Network Lists'); ?>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[nat_net_list_id]',
                                   'help' => 'When receiving a REGISTER or INVITE, enable NAT mode automatically if IP address in Contact header matches an entry defined in the access list. ACL is a misnomer in this case because access will not be denied if the user contact IP does not match.',
                                   'hint' => 'Matches force NAT traversal mechanisms'
                                    ), 'NAT List:');
            echo netlists::dropdown('sipinterface[nat_net_list_id]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[inbound_net_list_id]',
                                   'help' => 'A network list that defines who will be allowed to skip user authentication when making inbound calls to the server. Setting this to none will require all requests to pass authentication (username and password challenge) before being allowed to proceed.',
                                   'hint' => 'Matches do not requre authentication'
                                    ), 'Inbound ACL:');
            echo netlists::dropdown('sipinterface[inbound_net_list_id]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[register_net_list_id]',
                                   'help' => 'A network list of devices who will always be allowed to register to the server with any username / password combination.  Setting this to none will require all registration request to have a valid username and password.',
                                   'hint' => 'Matches can register with no credentials'
                                    ), 'Register ACL:');
            echo netlists::dropdown('sipinterface[register_net_list_id]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Inbound Calls'); ?>

        <div class="field">
        <?php
            echo form::label('sipinterface[context_id]', 'Default Incoming Context:');
            echo numbering::selectContext(array(
                    'name' => 'sipinterface[context_id]',
                    'all' => TRUE
                ),
                $sipinterface['context_id']
            );
        ?>
        </div>

    <?php echo form::open_section('Default Behaviors for Connected Devices'); ?>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[registry][detect_nat_on_registration]',
                                   'hint' => 'Detect SIP NAT IP & Port on Registration',
                                   'help' => 'If this box is checked, FreeSWITCH will check, on registration, if the device is advertising an IP & Port that is different then where the packet is coming from. If so, it will guess as to whether or not the device is behind NAT and will attempt to use the detected IP & Port on future SIP messages instead of the IP & Port the device told us to communicate with. This can do more harm then good - use it wisely.'
                                   ), 'Aggressive NAT Detection');
            echo form::checkbox('sipinterface[registry][detect_nat_on_registration]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[registry][force_rport]',
                                   'hint' => 'Equivalent to forcing rport',
                                   'help' => 'If this box is checked, FreeSWITCH will ignore the IP & Port that the SIP packet contained for where to send media to and will instead send media to the same IP & Port it has detected receiving media on from this device. This is equivalent to forcing the rport setting available on some devices. It will fix a lot of connection issues automatically and will cause FreeSWITCH to act closer to how Asterisk acts in regards to network traffic but can break advanced features in FreeSWITCH (like bypass media mode) - use this wisely.'
                                   ), 'Use Network IP & Port for RTP');
            echo form::checkbox('sipinterface[registry][force_rport]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'sipinterface[registry][force_register_domain]',
                                   'hint' => 'Equivalent to forcing rport',
                                   'help' => 'All inbound registrations will be considered for this domain, ignoring the domain provided by the registration request.  Setting this to none uses the domain specified in the registration.'
                                   ), 'Force Registration Domain');
            echo locations::dropdown(array('name' => 'sipinterface[registry][force_register_domain]',
                                    'nullOption' => 'None',
                                    'multitenancy' => FALSE));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <?php echo form::close(TRUE); ?>
</div>
