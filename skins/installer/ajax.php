<?php
	/**
	 * This is the template layout for AJAX requests made for the phonebooth skin
	 * Created by K Anderson 06-07-09
	 * 
	 */
?>
<?php echo $js; ?>
<?php echo $css; ?>


<!-- AJAX CONTENT -->
<?php echo $content; ?>
<!-- END AJAX CONTENT -->


<?php jquery::buildResponse(); ?>