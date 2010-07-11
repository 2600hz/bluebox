<div class="form">
<?php echo form::open();?>
<fieldset>
<legend>Scan for Devices</legend>
<?php echo form::input('ip', $_SERVER['HTTP_HOST']);?>
<?php echo form::dropdown(array('name' => 'cidr', 'class' => 'text'), array('8' => '8', '16' => '16', '24' => '24'), 24);?>
<?php echo form::submit('scan', 'Scan');?>

<div class="clearfix"></div>
<label for="supportedOnly">Show only supported devices</label>
<?php echo form::checkbox('supportedOnly', 'true', true);?>

<div class="clearfix"></div>
<table class="fancy" width="100%">
	<tr>
		<th width="20%">Status</th>
		<th width="20%">MAC</th>
		<th width="20%">IP</th>
		<th width="20%">Vendor</th>
		<th width="20%">Action</th>
	</tr>

<?php
// status, mac, ip. vendor
foreach($devices as $device) { 
	$class = ($device['supported']) ? 'supported' : 'unsupported';
	$action = ($device['supported']) ? html::anchor('provisioner/add/' . $device['mac'],'Add') : "N/A";
echo "<tr class=\"$class\">
			<td>{$device['vendor']}</td>
			<td>{$device['mac']}</td>
			<td><a href=\"http://{$device['ip']}/\" target=\"_blank\">{$device['ip']}</a></td>
			<td>{$device['vendor']}</td>
			<td>" . $action . "</td>
		</tr>\n";

} ?>
</table>

</fieldset>
<?php echo form::close();?>
</div>

<?php javascript::codeBlock(); ?>
    $('.unsupported').hide();
    $("#supportedOnly").bind("click", function(){
        if( $("#supportedOnly").attr('checked') )
        {
            $('.unsupported').hide();
        } else {
            $('.unsupported').show();
        }
    });
<?php javascript::blockEnd(); ?>
