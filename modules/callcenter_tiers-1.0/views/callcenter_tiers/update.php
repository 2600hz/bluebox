<?php defined('SYSPATH') or die('No direct access allowed.');

javascript::codeBlock(NULL, array('scriptname' => 'updateAgentsQueues')); ?>
	$.ajaxSetup( {cache : false});
<?php javascript::blockEnd(); ?>

<script language="javascript">
	function updateAgentsQueues(locid) {
		$.get('<?php echo url::base() ?>index.php/callcenter_tiers/updateagentdropdownbylocation/'+locid, function(data) {$('#callcenter_tier_cct_cca_id').html(data);});
		$.get('<?php echo url::base() ?>index.php/callcenter_tiers/updatequeuedropdownbylocation/'+locid, function(data) {$('#callcenter_tier_cct_ccq_id').html(data);});
	}
</script>

<div id="callcenter_update_header" class="txt-center update callcenter module_header">
	<h2><?php echo $mode=='create'?'Create':'Edit' ?> Tier</h2>
</div>
<div id="callcenter_update_form" class="update callcenter">    
	<?php echo form::open(); ?>
		<?php echo form::open_section(''); ?>

			<div class="field">
			<?php
			echo form::label(array(
				'for' => 'location'),
				'Location:'
			);
			echo form::dropdown(array('name'=>'location', 'onChange' => 'updateAgentsQueues(this.value)'), $locations, null, (isset(Router::$arguments[0]) && Router::$arguments[0] != 'null' ? 'disabled' : ''));
			?>
			</div>

			<div class="field">
			<?php
				echo form::label(array(
				'for' => 'callcenter_tier[cct_cca_id]',
				),
				'Agent:'
			);
			echo form::dropdown(array('name'=>'callcenter_tier[cct_cca_id]'), $agentlist, null, (isset(Router::$arguments[1]) && Router::$arguments[1] != 'null' ? 'onFocus="this.blur()"' : ''));
			?>
			</div>
		
			<div class="field">
			<?php
			echo form::label(array(
				'for' => 'callcenter_tier[cct_ccq_id]'),
				'Queue:'
			);
			echo form::dropdown(array('name'=>'callcenter_tier[cct_ccq_id]'), $queuelist, null, (isset(Router::$arguments[2]) && Router::$arguments[2] != 'null' ? 'onFocus="this.blur()"' : ''));
			?>
			</div>

			<div class="field">
			<?php
			echo form::label(array(
				'for' => 'callcenter_tier[cct_level]',
				'hint' => 'Tier within the call queue',
				'help' => 'When \'Use Tiering Rules?\' is set to yes in the queue configuration, this allows you to put agents at different tiers, requiring calls to wait in the queue for the specified period of time before connecting to higher tiered agents.'
				),
				'Agent Tier:'
			);
			echo form::input(array('name'=>'callcenter_tier[cct_level]'));
			?>
			</div>

			<div class="field">
			<?php
			echo form::label(array(
				'for' => 'callcenter_tier[cct_position]',
				'hint' => 'Order of answering',
				'help' => 'When \'Use Tiering Rules?\' is set to yes in the queue configuration, you can specifiy in what order the agents will be tried.'
				),
				'Agent Position:'
			);
			echo form::input(array('name'=>'callcenter_tier[cct_position]'));
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
