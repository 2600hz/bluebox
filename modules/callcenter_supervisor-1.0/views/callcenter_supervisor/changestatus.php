<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callcenter_supervisor_header" class="modules callcenter_supervisor module_header">
    <h2><?php echo __('Change Status')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('agentid', $agentid);
echo 'Change the status for agent ' . $agentdisplayname . ' (' . $agentlogindomain . ') to';
echo form::dropdown('status', Kohana::config('callcenter_supervisor.agent_status_options'));
echo form::close('ok_cancel');
?>
