<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<div id="sharedpresence_header" class="txt-center settings module_header">
    <h2><?php echo _('Shared Presence Database'); ?></h2>
</div>
<div id="sharedpresence_update_form" class="update">
    <?php echo form::open();
    echo form::open_section('');?>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sharedpresencedb[spd_name]',
                    'hint' => 'DB Name',
                    'help' => 'Short name for this shared appearence database.'
                ),
                'Name:'
            );
            echo form::input('sharedpresencedb[spd_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'sharedpresencedb[spd_desc]',
                    'hint' => 'Description of DB',
                    'help' => 'Detailed description of what shared appearences this DB is used for.'
                ),
				'Description:'
            );
            echo form::textarea(array('name' => 'sharedpresencedb[spd_desc]', 'rows' => 4, 'cols' => 50));
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
