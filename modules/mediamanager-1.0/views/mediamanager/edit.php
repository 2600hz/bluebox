<div id="mediamanager_edit_header" class="mediamanager edit module_header">
    <h2><?php echo $title; ?></h2>
</div>

<div id="mediamanager_edit_form" class="mediamanager edit">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Update file description'); ?>
    
        <div class="field">
        <?php
            echo form::label('mediamanager[description]', 'Description');
            echo form::textarea('mediamanager[description]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <?php echo form::close(TRUE); ?>
</div>

