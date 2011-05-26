<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<div id="paging_header" class="txt-center settings module_header">
    <h2><?php echo _('Paging Group'); ?></h2>
</div>
<div id="paging_update_form" class="update">
    <?php echo form::open();
    echo form::open_section('');?>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'paginggroup[pgg_name]',
                    'hint' => 'Group Name',
                    'help' => 'Short name for this paging group.'
                ),
                'Name:'
            );
            echo form::input('paginggroup[pgg_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'paginggroup[pgg_desc]',
                    'hint' => 'Description of Paging Group',
                    'help' => 'Detailed description of what this paging group is used for.'
                ),
				'Description:'
            );
            echo form::textarea(array('name' => 'paginggroup[pgg_desc]', 'rows' => 4, 'cols' => 50));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'paginggroup[pgg_device_ids]',
                    'hint' => 'Paging group members',
                    'help' => 'Devices that are members of this paging group.'
                ),
				'Group Members:'
            );
            echo form::dualListBox('paginggroup[pgg_device_ids]', $devicelist);
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
