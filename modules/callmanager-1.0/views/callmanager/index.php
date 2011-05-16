<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div id="callmanager_header_wrapper" name="callmanager_header_wrapper">
        <div id="callmanager_title"><h2>Call Manager</h2></div>
        <div id="callmanager_status"><img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif"></div>
        <div class="clear"></div>
</div>

<form name="optionform" id="optionform">
<div id="channel_list_options_prompt" name="channel_list_options_prompt">Show Filter and Sort Options [+]</div>
<div id="channel_list_options" name="channel_list_options">
        <div id="callstate_option_wrapper" name="callstate_option_wrapper">
                <div id="callstate_option_prompt" name="callstate_option_prompt">Include calls that are in state:</div>
                <div class="clear"></div>
                <div id="callstate_option_list" name="callstate_option_list">
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_NEW" value="CS_NEW"/>New<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_INIT" value="CS_INIT"/>Initialized<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_ROUTING" value="CS_ROUTING"/>Routing (looking for extension)<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_SOFT_EXECUTE" value="CS_SOFT_EXECUTE" checked/>Waiting for 3rd party control<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_EXECUTE" value="CS_EXECUTE" checked/>Executing dialplan<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_EXCHANGE_MEDIA" value="CS_EXCHANGE_MEDIA" checked/>Connected to another channel<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_PARK" value="CS_PARK" checked/>Parked/On Hold/Waiting for commands<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_CONSUME_MEDIA" value="CS_CONSUME_MEDIA"/>Consuming and dropping media<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_HIBERNATE" value="CS_HIBERNATE"/>Sleeping<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_RESET" value="CS_RESET"/>Reset<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_HANGUP" value="CS_HANGUP"/>Hanging Up<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_REPORTING" value="CS_REPORTING"/>Reporting<br>
                        <input type="checkbox" name="state_filters[]" id="state_filters_CS_DESTROY" value="CS_DESTROY"/>Waiting for cleanup<br>
                </div>
        </div>
        <div id="callstate_order_wrapper" name="callstate_order_wrapper">
                <div id="callstate_order_prompt" name="callstate_order_prompt">Order calls by:</div>
                <div class="clear"></div>
                <div id="callstate_order_list" name="callstate_order_list">
                        <?php
                        foreach ($summaryfields as $fieldname => $label)
                                echo '<input type="radio" name="channel_order" id="channel_order_' . $fieldname . '" value="' . $fieldname . ($fieldname == 'state' ? '" checked' : '"') . '/>' . $label . '<br>';
                        ?>
                </div>
        </div>
        <div class="clear"></div>
</div>
<div id="update_button" name="update_button"><button id="update" name="update" value="update" class="update small_green_button button" onClick="getChannelList(); return false;">Update</button></div>
</form>
<div class="clear"></div>
<div id="callmanager_channellist" name="callmanager_channellist"></div>
<?php
jquery::addPlugin(array('growl', 'blockUI'));
?>
<script language="javascript">
    function getChannelList() {
        $('#callmanager_status').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $.post("<?php echo url::base() ?>index.php/callmanager/getchannellist", $('#optionform').serialize(), function(data) {$('#callmanager_channellist').html(data);$('#callmanager_status').html('');});
    }
</script>
<?php javascript::codeBlock(); ?>
$('#channel_list_options_prompt').click(function(){
        details = $(this);
        parameters = $(this).parent().find('#channel_list_options');
        displayed = parameters.attr('displayed');
        if (displayed == 'true') {
                $(details).text('Show Filter and Sort Options [+]');
                parameters.attr('displayed', 'false');
                parameters.hide();
        } else {
                $(details).text('Hide Filter and Sort Options [--]');
                parameters.attr('displayed', 'true');
                parameters.show();
        }
});
        getChannelList();
<?php javascript::blockEnd();?>
