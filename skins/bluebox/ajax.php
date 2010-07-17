<?php echo $js; ?>
<?php echo $css; ?>

<?php message::render(); ?>

<?php
    $content = (string)$content;

    if (empty($content))
    {
        echo 'NO CONTENT';
    }
    else
    {
        echo $content;
    }
?>

<?php javascript::renderCodeBlocks(); ?>