<?php echo form::open_section('Dial By Name'); ?>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'directory[directory_full_name]',
			'hint' => 'Full name in [FirstName LastName] format.',
			'help' => 'Name to use for the directory in the format [FirstName Lastname].  Will default to the firstname and lastname of the associated user if this is left blank.'
		),
		'Name:'
	);
	echo form::input('directory[directory_full_name]', null);
?>
</div>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'directory[directory-visible]',
			'hint' => 'Check to include this number in the directory.',
			'help' => 'If checked, this name will be included in the directory.  If not, it will be hidden from the directory.'
		),
		'Include in Directory:'
	);
	echo form::checkbox('directory[directory-visible]', null, TRUE);
?>
</div>
<div class="field">
<?php
	echo form::label(
		array(
			'for' => 'directory[directory-exten-visible]',
			'hint' => 'Say the extension after match.',
			'help' => 'Announce the extension to the caller after a match is found.  This allows callers to bypass the directory in the future.'
		),
		'Say Extension:'
	);
	echo form::checkbox('directory[directory-exten-visible]', null, FALSE);
?>
</div>
<?php echo form::close_section(); ?>
