<div id="<?php echo $baseModel; ?>_delete_header" class="delete <?php echo $baseModel; ?> module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="<?php echo $baseModel; ?>_delete_form" class="delete <?php echo $baseModel; ?>">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Confirm'); ?>

    <div class="delete_warning">

        <?php echo __('Are you sure you want to delete the ' .$baseModel .' ' .$name .'?'); ?>
        
    </div>

    <?php echo form::close_section(); ?>

    <?php 
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(form::BUTTONS_DELETE_CANCEL); ?>

</div>