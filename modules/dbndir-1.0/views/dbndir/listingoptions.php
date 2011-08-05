<?php echo form::open_section('Dial By Name'); ?>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'dbndir[full_name]',
			'hint' => 'Full name in [FirstName LastName] format.',
			'help' => 'Name to use for the directory in the format [FirstName Lastname].  Will default to the firstname and lastname of the associated user if this is left blank.'
		),
		'Name:'
	);
	echo form::input('dbndir[full_name]');
?>
</div>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'dbndir[visible]',
			'hint' => 'Check to include this number in the directory.',
			'help' => 'If checked, this name will be included in the directory.  If not, it will be hidden from the directory.'
		),
		'Include in Directory:'
	);
	echo form::checkbox('dbndir[visible]');
?>
</div>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'dbndir[announce_ext]',
			'hint' => 'Say the extension after match.',
			'help' => 'Announce the extension to the caller after a match is found.  This allows callers to bypass the directory in the future.'
		),
		'Say Extension:'
	);
	echo form::checkbox('dbndir[announce_ext]');
?>
</div>
<?php echo form::close_section(); ?>
