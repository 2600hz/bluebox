<?php echo form::open_section('Redbox'); ?>

	<div class="field">
		<?php
			echo form::label('redbox[port_uri]', 'Associated Redbox:');

			$redbox_choices = array('0?0?0?0?0?0' => 'None');

			foreach($redboxes as $redbox)
			{
				foreach($redbox['ports'] as $port_num => $redbox_port)
				{
					if($redbox_port['type'] != 'lan')
					{
						continue;
					}

					$port_uri = $redbox_port['ip'] . '?' . $redbox_port['sipport'] . '?' . $redbox_port['vlan'] . '?' . $redbox_port['vlan_voice'] . '?' . $redbox_port['vlan_data'] . '?' . $port_num;

					$redbox_choices[$redbox['name']][$port_uri] = $redbox['port' . $port_num . '_label'];
				}
			}
			
			echo form::dropdown('redbox[port_uri]', $redbox_choices);
		        echo form::input(array('name' => 'endpointdevice[proxy_ip]', 'class' => 'hidden')); 
		        echo form::input(array('name' => 'endpointdevice[proxy_port]', 'class' => 'hidden')); 
		        echo form::input(array('name' => 'endpointdevice[vlan]', 'class' => 'hidden')); 
	      	        echo form::input(array('name' => 'endpointdevice[voice_vlan]', 'class' => 'hidden')); 
	 	        echo form::input(array('name' => 'endpointdevice[data_vlan]', 'class' => 'hidden')); 
		?>
        </div>
        
<?php echo form::close_section(); ?>

<script type="text/javascript">
    $(function()
    {
    	$('#redbox_port_uri').change(function()
	{
		uri = $(this).val();
		info = uri.split('?');
		$('#endpointdevice_proxy_ip').val(info[0]);
		$('#endpointdevice_proxy_port').val(info[1]);
		$('#endpointdevice_vlan').val(info[2]);
		$('#endpointdevice_voice_vlan').val(info[3]);
		$('#endpointdevice_data_vlan').val(info[4]);
	});
    });
</script>
