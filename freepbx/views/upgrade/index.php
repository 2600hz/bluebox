<?php message::render(); ?>
<div class="txt-center form">
<h2>Current Version: <?php echo $currentVersion;?></h2>
<h1 class="error">Warning, migrations to a lower revsion number may result in loss of data!</h1>
</div>

<div class="form">
<p>Selection the revsion of the model generated database you would like to migrations to.</p>
<?php
echo form::open();
echo form::dropdown('version', $versions);
echo html::br();
echo form::submit('migrate', 'Migrate');
echo form::close();
?>
</div>
