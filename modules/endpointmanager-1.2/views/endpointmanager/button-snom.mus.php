<div id='button_<?php print $button; ?>' class='assign_number_tab'>
	<div class="field">
		<?php
			print form::label(array(
				'for'=>"buttons[$button][type]",
				'hint'=>'Type of button',
				), "Type of button:");
			
			print form::dropdown(array("name"=>"buttons[$button][type]","onChange"=>"display_button_subdiv($button);"), array(
				"none"=>"Unused",
				"sipaccount"=>"My line",
				"blf"=>"Other line",
				"keyevent"=>"Special functions",
				"speeddial"=>"Speed-dial",
				"internal_dial"=>"Internal number",
				"external_dial"=>"External number",
				), $buttondata["type"]);

		?>
	</div>
	<div class="field" id="buttons_<?php print $button; ?>_type_none"></div>
	<div class="field" id="buttons_<?php print $button; ?>_type_sipaccount">
		<?php
			print form::label(array(
				'for'=>"buttons[$button][sipaccount]",
				'hint'=>'Line for this button',
				), "Line for this button:");
			print form::dropdown("buttons[$button][sipaccount]",$linedropdown,$buttondata["sipaccount"]);
		?>
	</div>
	<div class="field" id="buttons_<?php print $button; ?>_type_blf">
		<?php
			print form::label(array(
				'for'=>"buttons[$button][blf]",
				'hint'=>'Other extension to monitor',
				), "Other extension to monitor:");
			print form::dropdown("buttons[$button][blf]",$devices,$buttondata["blf"]);
		?>
	</div>
	<div class="field" id="buttons_<?php print $button; ?>_type_speeddial">
		<?php
			print form::label(array(
				'for'=>"buttons[$button][speeddial]",
				'hint'=>'Speed-Dial',
				), "Speed dial:");
			print form::dropdown("buttons[$button][speeddial]", $speeddials, $buttondata["speeddial"]);
		?>
	</div>
	<div class="field" id="buttons_<?php print $button; ?>_type_keyevent">
		<?php
			print form::label(array(
				'for'=>"buttons[$button][keyevent]",
				'hint'=>'Special Functions',
				), "Special Functions:");
			print form::dropdown("buttons[$button][keyevent]", $keyeventfunctions, $buttondata["keyevent"]);
		?>
	</div>
	<div id="buttons_<?php print $button; ?>_type_internal_dial">
		<div class="field">
			<?php
				print form::label(array(
					'for'=>"buttons[$button][internal_dial_label]",
					), "Label:");
				print form::input("buttons[$button][internal_dial_label]",$buttondata["internal_dial_label"]);
			?>
		</div>
		<div class="field">
			<?php
				print form::label(array(
					'for'=>"buttons[$button][internal_dial]",
					'hint'=>'Any service extension',
					), "Internal number to dial:");
				print form::input("buttons[$button][internal_dial]",$buttondata["internal_dial"]);
			?>
		</div>
	</div>
	<div id="buttons_<?php print $button; ?>_type_external_dial">
		<div class="field">
			<?php
				print form::label(array(
					'for'=>"buttons[$button][external_dial_label]",
					), "Label:");
				print form::input("buttons[$button][external_dial_label]",$buttondata["external_dial_label"]);
			?>
		</div>
		<div class="field">
			<?php
				print form::label(array(
					'for'=>"buttons[$button][external_dial]",
					'hint'=>'Do not include leading "1"',
					), "External number to dial:");
				print form::input("buttons[$button][external_dial]",$buttondata["external_dial"]);
			?>
		</div>
	</div>
</div>
<script>display_button_subdiv(<?php print $button; ?>);</script>
