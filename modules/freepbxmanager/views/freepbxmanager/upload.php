<h2 class="txt-center">Package Upload</h2>
<div class="form whole  pos-center">
<?=form::open_multipart('file/add');?>
<fieldset class="half">
<legend>
Select a package to upload
</legend>
<div>
<p class="txt-left">Package names have the format &lt;package&gt;.package.tar.gz. For example, blockcaller.package.tar.gz would be a valid package recognized by FreePBX.
The package must have one directory only in the root which matches the package name exactly. All of the plugin folders much reside under that directory.</p>

	
	<label for"upload">File Selection</label>
	<?=form::upload("upload", '');?>
	
	<div class="clear"></div>
	<?=form::hidden('type', 'package');?>
	<?=form::submit('submit', 'Upload');?>
	</div>
</fieldset>
<?=form::close();?>
</div>
