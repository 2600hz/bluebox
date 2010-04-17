<div id="locationmanager_update_header" class="update locationmanager module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="locationmanager_update_form" class="update locationmanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Location Information'); ?>

        <div class="field">
        <?php
            echo form::label('location[name]', 'Location Name:');
            echo form::input('location[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('location[domain]', 'Domain Name:');
            echo form::input('location[domain]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>