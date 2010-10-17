<div id="welcome_container">

    <div id="conferences_update_header" class="update conferenece module_header">
        
        <h2><?php echo __('Welcome to ' . $product . '!'); ?></h2>

    </div>

</div>

<?php
    if (isset($views))
    {
        echo subview::renderAsSections($views);
    }
?>