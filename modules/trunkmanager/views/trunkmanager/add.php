<div id="trunk_update_header" class="update trunk module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="trunk_update_form" class="update trunk">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Select Trunk Type'); ?>

        <div class="field">
        <?php
            echo form::label('trunk[class_type]', 'Trunk Type:');
            echo form::dropdown('trunk[class_type]', $supportedTypes);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Next->'); ?>
    </div>

    <?php echo form::close(); ?>
</div>