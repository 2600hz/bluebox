		<tr>
			<td colspan="2">		
				<table class="installStep" cellpadding="0" cellpadding="0">
										
					<tr>
						<th colspan="2"><?php echo __('Choose Install Method'); ?></th>
					</tr>
					
					<tr>
						<td width="50%" style="font-weight: bold;">
                            <?php echo __('New Installation'); ?>
							<br />
							<small><?php echo __('Fresh install removing any existing data'); ?></small>
						</td>
						<td width="50%" style="text-align: left; font-weight: bold;">
							<?php echo form::radio('installMode', 'install', $installMode == 'install' ? true : false); ?>
						</td>		
					</tr>
					
					<tr bgcolor="#eeeeee">		
						<td width="50%" style="font-weight: bold;">
                            <?php echo __('Upgrade Existing'); ?>
							<br />
							<small><?php echo __('Migrate an existing install to this version'); ?></small>
						</td>
						<td width="50%" style="text-align: left; font-weight: bold;">
							<?php echo form::radio('installMode', 'upgrade', $installMode == 'upgrade' ? true : false); ?>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>