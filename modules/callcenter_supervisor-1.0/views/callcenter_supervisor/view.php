<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div id="callcenter_supervisor_header" class="modules callcenter_supervisor module_header">
    <h2><?php echo __('Queue Status') . ' - ' . $queue_name_location; ?></h2>
</div>

<div id="agent_list_header" name="agent_list_header" class="agent_list_header">
        <div id="agent_list_label" name="agent_list_label" class="agent_list_label">Agent Status</div>
        <div id="agent_list_status" name="agent_list_status" class="agent_list_status"></div>
</div>
<div class="clear"></div>
<form name="agentoptionform" id="agentoptionform">
<?php echo form::hidden('queueid', $queueid);?>
<div id="agent_options_prompt" name="agent_options_prompt">Show Filter and Sort Options [+]</div>
<div id="agent_options" name="agent_options">
        <div id="agentstatus_option_wrapper" name="callstate_option_wrapper">
                <div id="agentstatus_option_prompt" name="callstate_option_prompt">Include Agents with Status:</div>
                <div class="clear"></div>
                <div id="agentstatus_option_list" name="agentstatus_option_list">
                        <input type="checkbox" name="agentstatus_filters[]" id="agentstatus_filters_Logged_Out" value="Logged Out" checked/>Logged Out<br>
                        <input type="checkbox" name="agentstatus_filters[]" id="agentstatus_filters_Available" value="Available" checked/>Available<br>
                        <input type="checkbox" name="agentstatus_filters[]" id="agentstatus_filters_On_Demand" value="Available (On Demand)" checked/>Available (On Demand)<br>
                        <input type="checkbox" name="agentstatus_filters[]" id="agentstatus_filters_On_Break" value="On Break" checked/>On Break<br>
                </div>
        </div>
        <div id="agentstatus_order_wrapper" name="agentstatus_order_wrapper">
                <div id="agentstatus_order_prompt" name="agentstatus_order_prompt">Order agents by:</div>
                <div class="clear"></div>
                <div id="agentstatus_order_list" name="agentstatus_order_list">
                        <?php
                        foreach ($agent_status_fields as $fieldname => $label)
                                echo '<input type="radio" name="agent_order" id="agent_order_' . $fieldname . '" value="' . $fieldname . ($fieldname == 'displayname' ? '" checked' : '"') . '/>' . $label . '<br>';
                        ?>
                </div>
        </div>
        <div class="clear"></div>
</div>
<div id="update_button" name="update_button"><button id="update" name="update" value="update" class="update small_green_button button" onClick="getAgentStatus(); return false;">Update Agents</button></div>
</form>
<div class="clear"></div>
<div id="agent_list_table" class="agent_list_table"></div>
<div class="clear"></div>
<div class="listseperator"></div>
<div id="queue_list_header" name="queue_list_header" class="queue_list_header">
        <div id="queue_list_label" name="queue_list_label" class="queue_list_label">Calls in Queue</div>
        <div id="queue_list_status" name="queue_list_status" class="queue_list_status"></div>
</div>
<div class="clear"></div>
<form name="queueoptionform" id="queueoptionform">
<?php echo form::hidden('queueid', $queueid);?>
<div id="queue_options_prompt" name="queue_options_prompt">Show Filter and Sort Options [+]</div>
<div id="queue_options" name="queue_options">
        <div id="queue_option_wrapper" name="queue_option_wrapper">
                <div id="queue_option_prompt" name="queue_option_prompt">Include calls that are in state:</div>
                <div class="clear"></div>
                <div id="queue_option_list" name="queue_option_list">
                        <input type="checkbox" name="queuestate_filters[]" id="queuestate_filters_Unknown" value="Unknown" checked/>Unknown<br>
                        <input type="checkbox" name="queuestate_filters[]" id="queuestate_filters_Waiting" value="Waiting" checked/>Waiting<br>
                        <input type="checkbox" name="queuestate_filters[]" id="queuestate_filters_Trying" value="Trying" checked/>Trying<br>
                        <input type="checkbox" name="queuestate_filters[]" id="queuestate_filters_Answered" value="Answered" checked/>Answered<br>
                        <input type="checkbox" name="queuestate_filters[]" id="queuestate_filters_Abandoned" value="Abandoned" checked/>Abandoned<br>
                </div>
        </div>
        <div id="queue_order_wrapper" name="queue_order_wrapper">
                <div id="queue_order_prompt" name="queue_order_prompt">Order calls by:</div>
                <div class="clear"></div>
                <div id="queue_order_list" name="queue_order_list">
                        <?php
                        foreach ($queue_status_fields as $fieldname => $label)
                                echo '<input type="radio" name="queue_order" id="queue_order_' . $fieldname . '" value="' . $fieldname . ($fieldname == 'base_score' ? '" checked' : '"') . '/>' . $label . '<br>';
                        ?>
                </div>
        </div>
        <div class="clear"></div>
</div>
<div id="update_button" name="update_button"><button id="update" name="update" value="update" class="update small_green_button button" onClick="getQueueStatus(); return false;">Update Calls</button></div>
</form>
<div class="clear"></div>
<div id="queue_list_table" class="queue_list_table"></div>
<div class="clear"></div>

<?php
jquery::addPlugin(array('growl', 'blockUI'));
?>
<script language="javascript">
    function getQueueStatus() {
        $('#queue_list_status').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $.post("<?php echo url::base() ?>index.php/callcenter_supervisor/getQueueList", $('#queueoptionform').serialize(), function(data) {$('#queue_list_table').html(data);$('#queue_list_status').html('');});
    }

    function getAgentStatus() {
        $('#agent_list_status').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $.post("<?php echo url::base() ?>index.php/callcenter_supervisor/getAgentList", $('#agentoptionform').serialize(), function(data) {$('#agent_list_table').html(data);$('#agent_list_status').html('');});
    }
</script>
<?php javascript::codeBlock(); ?>
        $('#queue_options_prompt').click(function(){
                details = $(this);
                parameters = $(this).parent().find('#queue_options');
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
        $('#agent_options_prompt').click(function(){
                details = $(this);
                parameters = $(this).parent().find('#agent_options');
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
        getQueueStatus();
        getAgentStatus();
<?php javascript::blockEnd();?>


