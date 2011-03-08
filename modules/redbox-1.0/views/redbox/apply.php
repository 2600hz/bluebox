<?php echo form::open_section('Redbox'); ?>

	<div class="field">
		<?php
			echo form::label('redbox[port_uri]', 'Associated Redbox:');

			$redbox_choices = array('0' => 'None');

			foreach($redboxes as $redbox)
			{
				foreach($redbox['ports'] as $port_num => $redbox_port)
				{
					if($redbox_port['type'] != 'lan')
					{
						continue;
					}

					$port_uri = $redbox_port['ip'] . '<>' . $redbox_port['sipport'] . '<>' . $port_num;

					$redbox_choices[$redbox['name']][$port_uri] = $redbox['port' . $port_num . '_label'];
				}
			}
			
			echo form::dropdown('redbox[port_uri]', $redbox_choices);
		?>
        </div>
        
	<?php
	      echo form::hidden('endpointdevice[host]'); 
	      echo form::hidden('endpointdevice[port]'); 
	?>

<?php echo form::close_section(); ?>

<script type="text/javascript">
    $(function()
    {
    	$('#redbox_port_uri').change(function()
	{
		uri = $(this).val();
		host = uri.replace(/<>.*$/, '');
		port = uri.replace(/^[^>]*>|<>[^<]*$/g, '');
		
		$('#endpointdevice_host_hidden').val(host);
		$('#endpointdevice_port_hidden').val(port);
	});
    });
</script>
