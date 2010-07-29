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

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>
    
    <?php echo form::close(); ?>
</div>