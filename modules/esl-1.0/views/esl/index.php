<div id="esl_body" class="esl">
    <textarea id="console" name="console">
 _____ ____  _
| ____/ ___|| |
|  _| \___ \| |
| |___ ___) | |___
|_____|____/|_____|

<?php if (empty($isConnected)): ?>
== CONNECTION ==
Failed to connect to ESL. Make sure FreeSWITCH is running...
Click an option below to test your connection again.
<?php else: ?>
== CONNECTION ==
Connected to FreeSWITCH, please choose and option below..
<?php endif; ?>

<?php if (empty($isExtension)): ?>
== ESL EXTENSION ==
The FreeSWITCH PHP/ESL module is not installed (or not working). We will fail back to socket-based/native ESL support.
This error is not critical, you can continue to use the ESL manager and choose to install the extension later.
See http://wiki.freeswitch.org/wiki/Event_Socket_Library for more information.
<?php else: ?>
== ESL EXTENSION ==
The FreeSWITCH PHP/ESL module is avaliable and loaded.
<?php endif; ?>

== HELP ==
Use the buttons below to send requests directly to FreeSWITCH. You can get help and more information in the IRC channel #bluebox-dev on irc.freenod.net.

    </textarea>
    <hr />
    <div id="esl">
            <h3><a href="#">System</a></h3>
            <div id="system">
                <?php echo form::button(array('id' => 'reloadacl', 'class' => 'eslEvent', 'name' => 'reloadacl', 'value' => 'Reload ACL'));?>
                <?php echo form::button(array('id' => 'reloadxml', 'class' => 'eslEvent', 'name' => 'reloadxml', 'value' => 'Reload XML'));?>
                <?php echo form::button(array('id' => 'status', 'class' => 'eslEvent', 'name' => 'status', 'value' => 'Status'));?>
                <?php echo form::button(array('id' => 'version', 'class' => 'eslEvent', 'name' => 'version', 'value' => 'Version'));?>
                <?php echo form::button(array('id' => 'sofia_status', 'class' => 'eslEvent','value' => 'Sofia Status'));?>
                <?php echo $sofia_status;?>
                <?php echo $trunk_status; ?>
            </div>

        <h3><a href="#">Channels</a></h3>
            <div id="channels">
                <?php echo form::button(array('id' => 'channels', 'class' => 'eslEvent', 'name' => 'channels', 'value' => 'Show all channels'));?>
            </div>

        <h3><a href="#">Calls</a></h3>
            <div id="calls">
                <?php echo form::button(array('id' => 'calls', 'class' => 'eslEvent', 'name' => 'calls', 'value' => 'Show all calls'));?>
            </div>

        <h3><a href="#">Show</a></h3>
            <div id="show">
                <?php echo form::button(array('id' => 'show_codec', 'class' => 'eslEvent', 'value' => 'Codecs'));?>
                <?php echo form::button(array('id' => 'show_modules', 'class' => 'eslEvent','value' => 'Modules'));?>
            </div>

        <h3><a href="#">NAT</a></h3>
            <div id="nat">
                <?php echo form::button(array('id' => 'nat_status', 'class' => 'eslEvent', 'name' => 'nat_status', 'value' => 'Get NAT status'));?>
                <?php echo form::button(array('id' => 'nat_reinit', 'class' => 'eslEvent', 'name' => 'nat_reinit', 'value' => 'Reinitialize NAT'));?>
                <?php echo form::button(array('id' => 'nat_republish', 'class' => 'eslEvent', 'name' => 'nat_republish', 'value' => 'Republish NAT'));?>
            </div>
        <h3><a href="#">Modules</a></h3>
            <div id="modules">
                <?php echo form::button(array('id' => 'reload_sofia', 'class' => 'eslEvent', 'value' => 'Reload Sofia'));?>
            </div>
        <h3><a href="#">Manual Entry</a></h3>
            <div id="modules">
                <?php
                    echo form::dropdown('type', array(
                        'sendRecv',
                        'recvEvent',
                    ));
                ?>
                
                <?php echo form::input('params'); ?>

                <?php echo form::button(array('id' => 'manual_entry', 'param' => 'version', 'class' => 'eslEvent', 'value' => 'Send'));?>
            </div>
    </div>
</div>

<?php
    jquery::addPlugin('accordion');
?>

<?php javascript::codeBlock(); ?>
    var eslEvent = 'sdf';

    $("#esl").accordion({
        icons: {
            header: "ui-icon-circle-arrow-e",
            headerSelected: "ui-icon-circle-arrow-s"
        }
    });

    $(".eslEvent").bind("click", function() {
        var type = $(this).attr("id");
        if (type == 'manual_entry') {
            type = $('#type option:selected').text();
            var param = $('#params').val();
        } else {
            var param = $(this).attr('param');
        }
        var dotCount = 6;
        ajaxLoading = setInterval (function () {
            if (dotCount > 4) {
                $('#console').html('<?php echo __('Please wait');?> ');
                dotCount = 0;
            } else {
                $('#console').append('. ');
                dotCount += 1;
            }
        }, 250);

        if(typeof(param) == "undefined") {
            param = false;
        }

        $.post('<?php echo url::site('esl/eslreponse');?>', { 'type' : type, 'param' : param }, function(data) {
            clearTimeout(ajaxLoading);
            $('#console').html(data);
       });
    });
<?php javascript::blockEnd(); ?>