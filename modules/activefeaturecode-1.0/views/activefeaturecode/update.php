<?php echo form::open_section('Active Feature Code'); ?>

    <div class="field">
    <?php
        echo form::label('activefeaturecode[type]', 'Type:');
	echo form::dropdown('activefeaturecode[type]', array('0' => 'None','transfer' => 'Transfer'));
    ?>
    </div>

    <div id="active_feature_code_options">
	    <div class="field">
	    <?php
		echo form::label('activefeaturecode[number]', 'Feature Code:');
		echo form::input('activefeaturecode[number]');
	    ?>
	    </div>

	    <div class="field">
		    <?php echo form::label('activefeaturecode[exten]', 'Destination: '); ?>
		    <?php
			if (isset($activefeaturecode['exten'])) {
			    $selectedClass = numbering::getAssignedPoolByNumber($activefeaturecode['exten']);
			}
			else {
			    $selectedClass = NULL;
			}

			echo numbering::poolsDropdown(array(
				'name' => 'activefeaturecode_class_type',
				'forDependent' => TRUE
			    ), $selectedClass
			);

			echo " named ";

			echo numbering::numbersDropdown(array(
			    'id' => 'activefeaturecode_inbound',
			    'name' => 'activefeaturecode[exten]',
			    'useNames' => TRUE,
			    'optGroups' => FALSE,
			    'forDependent' => TRUE
			), isset($activefeaturecode['exten']) ? $activefeaturecode['exten'] : NULL);

			jquery::addQuery('#activefeaturecode_inbound')->dependent('{ parent: \'activefeaturecode_class_type\', group: \'common_class\' }');
		    ?>
	    </div>

	    <div class="field">
	    <?php
		echo form::label('activefeaturecode[leg]', 'Allow feature code from: ');
		echo form::dropdown('activefeaturecode[leg]', array('b' => 'Callee','a' => 'Caller'));
	    ?>
	    </div>
    </div>

    <script type="text/javascript">
    	$(function() {

		<?php
			if((!arr::get($activefeaturecode, 'type')) || arr::get($activefeaturecode, 'type') == '0') {
				echo "$('#active_feature_code_options').hide();";
			}
		?>

		$('#activefeaturecode_type').change(function() {
			if($(this).val() != '0') {
				$('#active_feature_code_options').slideDown();
			} else {
				$('#active_feature_code_options').slideUp();
			}
		});
	});
    </script>
<?php echo form::close_section(); ?>
