<div id="my_module_update_header" class="update my_module module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="my_module_update_form" class="txt-left form my_module update">

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

    <?php echo form::close(TRUE); ?>

</div>