<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<div id="valetParking_header" class="txt-center settings module_header">
    <h2><?php echo _('Valet Parking'); ?></h2>
</div>
<div id="valetParking_update_form" class="update">
    <?php echo form::open();
    echo form::open_section('');?>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'valetparkinglot[vpl_name]',
                    'hint' => 'Lot Name',
                    'help' => 'Short Name for this parking lot.'
                ),
                'Name:'
            );
            echo form::input('valetparkinglot[vpl_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'valetparkinglot[vpl_desc]',
                    'hint' => 'Description of Parking Lot',
                    'help' => 'Detailed description of what this parking lot is used for.'
                ),
				'Description:'
            );
            echo form::textarea(array('name' => 'valetparkinglot[vpl_desc]', 'rows' => 4, 'cols' => 50));
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'valetparkinglot[vpl_start]',
                    'hint' => 'Starting Extension',
                    'help' => 'Lowest extension to begin parking calls on.'
                ),
                'Start Ext:'
            );
            echo form::input('valetparkinglot[vpl_start]');
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'valetparkinglot[vpl_end]',
                    'hint' => 'Ending Extension',
                    'help' => 'Highest extension to end parking calls on.'
                ),
                'End Ext:'
            );
            echo form::input('valetparkinglot[vpl_end]');
        ?>
        </div>
	<?php
		echo form::close_section();
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
        if ($mode !== 'view')
			echo form::close(TRUE);
		else
			echo form::close('ok_only');
		?>
</div>
