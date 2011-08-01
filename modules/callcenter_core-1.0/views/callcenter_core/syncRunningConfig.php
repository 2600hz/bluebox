<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<table name="syncRunningConfigTable" id="syncRunningConfigTable">
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow odd" id="syncRunningConfigRow_setup"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_setup">Setting Up</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_setup" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow group" id="syncRunningConfigRow_queuegroup"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_queuegroup" class="group">Queues</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_queuegroup" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow even" id="syncRunningConfigRow_buildrunningqueuelist"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_buildrunningqueuelist" class="sub">Building List of Running Queues</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_buildrunningqueuelist" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow odd" id="syncRunningConfigRow_builddbqueuelist"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_builddbqueuelist" class="sub">Building List of Queues in the database</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_builddbqueuelist" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow even" id="syncRunningConfigRow_reconcilequeues"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_reconcilequeues" class="sub">Reconciling Queues</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_reconcilequeues" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow group" id="syncRunningConfigRow_agentgroup"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_agentgroup" class="group">Agents</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_agentgroup" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow even" id="syncRunningConfigRow_buildrunningagentlist"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_buildrunningagentlist" class="sub">Reconciling Agents</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_buildrunningagentlist" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow odd" id="syncRunningConfigRow_builddbagentlist"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_builddbagentlist" class="sub">Building List of Agents in the database</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_builddbagentlist" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow even" id="syncRunningConfigRow_reconcileagents"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_reconcileagents" class="sub">Reconciling Agents</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_reconcileagents" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow group" id="syncRunningConfigRow_tiergroup"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_tiergroup" class="group">Tiers</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_tiergroup" class="status"></td></tr>
	<tr name="syncRunningConfigRow" class="syncRunningConfigRow even" id="syncRunningConfigRow_syncronizetiers"><td name="syncRunningConfigLabel" id="syncRunningConfigLabel_syncronizetiers" class="sub">Reconciling Tiers</td><td name="syncRunningConfigStatus" id="syncRunningConfigstatus_syncronizetiers" class="status"></td></tr>
</table>
<br>
<?php
echo form::open();
$data = array('name' => 'submit[' .Bluebox_Controller::SUBMIT_CONFIRM .']', 'class' => 'save small_green_button hide');
echo form::button($data, 'OK');
echo form::close(FALSE);
?>
<script language="javascript">


	function enableOKButton() {
		$('#submit_confirm__OK').removeClass('hide');
	}
	function clearWorking(el) {
		$(el).html("");
	}
	function setWorking(el) {
		$(el).html('<img src="<?php echo url::base() . skins::getSkin()?>assets/img/thinking.gif">');
	}
	function setOK(el) {
		$(el).html('<img src="<?php echo url::base() . skins::getSkin()?>assets/img/iconCheck.png">');
	}
	function setError(el, msg) {
		$(el).html('<img src="<?php echo url::base() . skins::getSkin()?>assets/img/iconX.png"><br> ' + msg);
	}
	function syncRunningConfig(step)
	{
		setWorking("#syncRunningConfigstatus_" + step);
		$.getJSON("<?php echo url::base() ?>index.php/callcenter_core/syncRunningConfig/" + step,
			null,
			function(data) {
				if (data.result == "OK")
				{
					setOK("#syncRunningConfigstatus_" + step);
				}
				else
				{
					setError("#syncRunningConfigstatus_" + step, data.message);
				}
				if (data.nextstep != '')
				{
					syncRunningConfig(data.nextstep);
				}
				else
				{
					enableOKButton();
				}
			}
		);
	}
</script>
<?php javascript::codeBlock(); ?>
	enableOKButton();
	$('#syncRunningConfigTable').ajaxError(
		function(e, xhr, settings, exception) {
			alert('An error occured during the configuration sync.  Please see system logs for details.');
			alert(settings);
			enableOKButton();
		}
	);

	syncRunningConfig('setup');
<?php javascript::blockEnd();?>
