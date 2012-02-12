<div id="feature_form_status">
</div>
<div id="feature_form">
	<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">
</div>
<?php javascript::codeBlock(); ?>
        $('.FeatureNumber').click(function(){
			getFeatureNumberOptionForm();
        });
        getFeatureNumberOptionForm();
<?php javascript::blockEnd();?>
<script language="javascript">
    function getFeatureNumberOptionForm() {
        $('#feature_form_status').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $('#feature_form').html("");
        $.post("<?php echo url::base() ?>index.php/feature/getFeatureNumberOptionsForm", $('form').serialize(), function(data) {$('#feature_form').html(data); $('#feature_form_status').html("");});
    }
</script>
