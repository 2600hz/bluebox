<div id="xmpp_update_header" class="update xmpp module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="xmpp_update_form" class="txt-left form xmpp update">

    <?php echo form::open(); ?>

    <?php echo form::open_section('XMPP Login Information'); ?>

        <div class="field">
           <?php echo form::label(array('for' => 'xmpp[name]',
                                        'help' => 'This field cannot contain any special characters'), 'Name:'); ?>
           <?php echo form::input('xmpp[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label(array('for' => 'xmpp[login]',
                                         'help' => 'If this is a Google Voice account, the user name should have /talk appended.' .
                                                   '<br><br><u>Example</u><br>#####@gmail.com/talk'), 'Login:'); ?>
            <?php echo form::input('xmpp[login]'); ?>
        </div>
        <div class="field">
            <?php echo form::label('xmpp[registry][password]', 'Password:'); ?>
            <?php echo form::password('xmpp[registry][password]'); ?>
        </div>
        <div class="field">
            <?php echo form::label(array('for' => 'xmpp[registry][loginserver]',
                                         'help' => 'This is the server that is used to authenticate the username and password.' .
                                                   '<br>The login server <i>may</i> differ from the outboud server.' .
                                                   '<br><br><u>Example</u><br>For Google Voice, this setting should be: talk.google.com'),
                                   'Server:'); ?>
            <?php echo form::input('xmpp[registry][loginserver]', 'talk.google.com'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('XMPP Client Options'); ?>

        <div class="field">
            <?php echo form::label('xmpp[registry][message]', 'Message:'); ?>
            <?php echo form::input('xmpp[registry][message]'); ?>
        </div>
        <div class="field">
            <?php echo form::label('xmpp[registry][autoreply]', 'Auto-Reply:'); ?>
            <?php echo form::input('xmpp[registry][autoreply]'); ?>
        </div>
        <div class="field">
            <?php echo form::label(array('for' => 'xmpp[registry][autologin]',
	    				 'help' => 'This option should be enabled in most cases.'),
				   'Auto-Login:'); ?>
            <?php echo form::checkbox('xmpp[registry][autologin]'); ?>
        </div>
        <div class="field">
            <?php echo form::label(array('for' => 'xmpp[registry][usertptimer]',
                                         'help' => 'If you are having issues with audio delay, you can try disabling this.' . 
					 	   '<br>Otherwise this should be enabled.'), 
                                   'Use RTP Timer'); ?>
            <?php echo form::checkbox('xmpp[registry][usertptimer]'); ?>
        </div>
	<div class="field">
	    <?php echo form::label(array('for' => 'xmpp[registry][tls]',
	    				 'help' => 'This option needs to be enabled for Google Voice.'),
				   'TLS:'); ?>
            <?php echo form::checkbox('xmpp[registry][tls]'); ?>
	</div>

        <!-- HIDDEN FIELDS FOR NON-USER-CONFIGURABLE SETTINGS (well for now) -->
        <?php echo form::hidden('xmpp[registry][dialplan]', 'XML'); ?>
        <?php echo form::hidden('xmpp[registry][rtpip]', '$${bind_server_ip}'); ?>
        <?php echo form::hidden('xmpp[registry][sasl]', 'plain'); ?>
        <?php echo form::hidden('xmpp[registry][vad]', 'both'); ?>
        <?php echo form::hidden('xmpp[registry][candidateacl]', 'wan.auto'); ?>
        <?php echo form::hidden('xmpp[registry][localnetacl]', 'localnet.auto'); ?>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Inbound Routing'); ?>

        <div class="field">
            <?php echo form::label('xmpp[registry][exten]', 'Route incoming calls to:'); ?>
            <?php
                if (isset($xmpp['registry']['exten'])) {
                    $selectedClass = numbering::getAssignedPoolByNumber($xmpp['registry']['exten']);
                }
                else {
                    $selectedClass = NULL;
                }

                echo numbering::poolsDropdown(array(
                        'name' => 'xmpp_class_type',
                        'forDependent' => TRUE
                    ), $selectedClass
                );

                echo " named ";

                echo numbering::numbersDropdown(array(
                    'id' => 'xmpp_inbound',
                    'name' => 'xmpp[registry][exten]',
                    'useNames' => TRUE,
                    'optGroups' => FALSE,
                    'forDependent' => TRUE
                ), isset($xmpp['registry']['exten']) ? $xmpp['registry']['exten'] : NULL);

                jquery::addQuery('#xmpp_inbound')->dependent('{ parent: \'xmpp_class_type\', group: \'common_class\' }');
            ?>

        </div>

        <div class="field">
        <?php
            echo form::label('xmpp[registry][inbound_context]', 'Default Incoming Context:');
            echo numbering::selectContext('xmpp[registry][inbound_context]', isset($xmpp['registry']['inbound_context']) ? $xmpp['registry']['inbound_context'] : '');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Route Outbound Calls Matching...'); ?>

        <?php foreach ($outboundPatterns as $pattern): ?>

            <div class="field">

                <?php echo form::label('xmpp[registry][patterns][' . $pattern['simple_route_id'] .']', $pattern['name']); ?>

                <?php echo form::checkbox('xmpp[registry][patterns][' . $pattern['simple_route_id'] .'][enabled]'); ?>

                <span style="padding:0 5px 0;">Prepend calls with:</span>

                <?php echo form::input('xmpp[registry][patterns][' .$pattern['simple_route_id'] .'][prepend]'); ?>

            </div>
        
        <?php endforeach; ?>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Made from These Contexts...'); ?>

        <?php foreach ($contexts as $context): ?>

            <div class="field">

            <?php echo form::label('xmpp[registry][contexts][' .$context['context_id'] .']', $context['name']); ?>

            <?php echo form::checkbox('xmpp[registry][contexts][' .$context['context_id'] .']'); ?>

            </div>

        <?php endforeach; ?>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Outbound Server'); ?>
        <div class="field">
             <?php echo form::label(array('for' => 'xmpp[registry][outboundserver]',
                                          'help' => 'This is the server that is used for outbound calls.' .
                                                    '<br><br><u>Example</u><br> For Google Voice, this setting should be: voice.google.com'),
                                    'Server:'); ?>
             <?php echo form::input('xmpp[registry][outboundserver]', 'voice.google.com'); ?>
        </div>
    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

    <?php jquery::addPlugin(array('dependent')); ?>

</div>

