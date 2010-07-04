<?php echo $js; ?>
<?php echo $css; ?>

<?php message::render(); ?>

<?php echo ($content == "")? 'NO CONTENT' : $content; ?>

<?php javascript::renderCodeBlocks(); ?>