<div id="endpointmanager_update_header" class="update endpointmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="endpointmanager_update_form" class="txt-left form endpointmanager update">

    <?php echo form::open(); ?>

    <?php echo form::open_section('My Module'); ?>

        <div class="field">
            <?php echo form::label('mymodule[mydatafield1]', 'Field 1:'); ?>
            <?php echo form::input('mymodule[mydatafield1]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('mymodule[mydatafield2]', 'Field 2:'); ?>
            <?php echo form::input('mymodule[mydatafield2]'); ?>
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