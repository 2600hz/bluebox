<?php
defined('SYSPATH') or die('No direct access allowed.');

if ($mode==='edit')
{
	$currentuser = users::getCurrentUser();
	if ($currentuser['user_type'] < $feature['ftr_edit_user_type'])
	{
		$mode = 'view';
	}
}
?>
<div id="featureTypes_header" class="txt-center settings featureTypes module_header">
    <h2><?php echo $title; ?></h2>
</div>
<div id="featureTypes_update_form" class="update featureTypes">
    <?php echo form::open();
    echo form::open_section('');?>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'feature[ftr_name]',
                    'hint' => 'Feature Name',
                    'help' => 'Used to determine the name of the edit form and driver.'
                ),
                'Name:'
            );
            echo form::input('feature[ftr_name]', null, ($mode == 'view' ? 'onFocus="this.blur();" ' : '') . 'onChange="getFeatureTypeForm();"');
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'feature[ftr_display_name]',
                    'hint' => 'Display Name',
                    'help' => 'Short name for this feature that will be understandable in a drop down menu defining the feature code.'
                ),
                'Display Name:'
            );
            echo form::input('feature[ftr_display_name]', null, ($mode == 'view' ? 'onFocus="this.blur();"' : ''));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'feature[]',
                    'hint' => 'Description of feature',
                    'help' => 'Detailed description about what this feature does.'
                ),
				'Description:'
            );
            echo form::textarea(array('name' => 'feature[ftr_desc]', 'rows' => 4, 'cols' => 50), null, ($mode == 'view' ? 'onFocus="this.blur();"' : ''));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'feature[ftr_package_id]',
                    'hint' => 'Package associated with feature',
                    'help' => 'The package that provides the form and driver for the feature.'
                ),
				'Associated Package:'
            );
            echo form::dropdown('feature[ftr_package_id]', $packagelist, null, ($mode == 'view' ? 'onFocus="this.blur();"' : '') . 'onChange="getFeatureTypeForm();"');
        ?>
        </div>
        <?php echo form::hidden('feature[ftr_id]');?>
        <div class="field">
<?php
		if ($mode != 'view')
		{
            echo form::label(array(
                    'for' => 'feature[ftr_edit_user_type]',
                    'hint' => 'User level required to edit this feature',
                    'help' => 'Users below this level will only be able to view this feature.'
                ),
				'User Level Required to edit:'
            );
            echo form::dropdown('feature[ftr_edit_user_type]', $usertypelist);
?>
			</div>
<?php
		}
?>
		<div id="featuretypeformstatus" name="featuretypeformstatus"></div>
		<div id="featuretypeform" name="featuretypeform">
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
<script language="javascript">
    function getFeatureTypeForm() {
        $('#featuretypeformstatus').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $('#featuretypeform').html("");
        $.post("<?php echo url::base() ?>index.php/feature/getFeatureForm", $('form').serialize(), function(data) {$('#featuretypeform').html(data); $('#featuretypeformstatus').html("");});
    }
</script>
<?php javascript::codeBlock(); ?>
	getFeatureTypeForm();
<?php javascript::blockEnd();?>