<div id='line_<?php print $line; ?>' class='assign_number_tab'>
	<div class="field">
		<?php
			print form::label(array(
				'for'=>"lines[$line][use]",
				'hint'=>'Use for this line',
				), "Use for this line:");
			print form::dropdown(array("name"=>"lines[$line][sip]","onChange"=>"change_line($line)"),$devices,$linedata["sip"]);
		?>
	</div>
</div>

