<div id="contextmanager_update_header" class="txt-center update contextmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="contextmanager_update_form" class="update contextmanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Context Entry'); ?>

        <div class="field">
        <?php
            echo form::label('context[name]', 'Context Name:');
            echo form::input('context[name]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>
    
</div>