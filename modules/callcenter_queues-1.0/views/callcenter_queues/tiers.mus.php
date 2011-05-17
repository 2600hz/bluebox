<?php defined('SYSPATH') or die('No direct access allowed.');
echo form::open_section('');
?>
        <h3>Agents that are members of this queue</h3>
	<div class="tier_quick_add"><?php echo html::anchor('callcenter_tiers/create/' . $this->callcenter_queue->_data['ccq_locationid'] . '/null/' . $this->callcenter_queue->_data['ccq_id'],'<span>' . __('Add Agents') .'</span>', array('class' => 'qtipAjaxForm')); ?></div>
	
<?php 
	echo $tiergrid;
	echo '<div style="width:100%; margin: 10px;">&nbsp;</div>';
echo form::close_section();
?>