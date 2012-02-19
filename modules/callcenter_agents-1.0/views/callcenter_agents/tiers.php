<?php defined('SYSPATH') or die('No direct access allowed.');
echo form::open_section('');
?>
        <h3>Queues Agent Is a Member Of</h3>
	<div class="tier_quick_add"><?php echo html::anchor('callcenter_tiers/create/' . $this->callcenter_agent->_data['cca_locationid'] . '/' . $this->callcenter_agent->_data['cca_id'] . '/null','<span>' . __('Add Membership to Queue') .'</span>', array('class' => 'qtipAjaxForm')); ?></div>
	
<?php 
	echo $tiergrid;
	echo '<div style="width:100%; margin: 10px;">&nbsp;</div>';
echo form::close_section();
?>