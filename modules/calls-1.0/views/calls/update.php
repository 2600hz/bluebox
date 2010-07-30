<div id="calls_view_header" class="view calls module_header">

    <h2><?php echo __($title); ?></h2>

</div>

<div id="calls_update_form" class="view calls">

    <?php echo form::open(); ?>

    <?php echo form::open_section(''); ?>

    <?php foreach ($coreFields as $field => $value): ?>

        <div class="field">
        <?php
            echo form::label("calls[$field]", $value);
            echo form::input("$field",$calls[$field],'disabled=disabled');
        ?>
        </div>

    <?php endforeach; ?>

    <?php foreach ($calls->custom_fields as $field => $value): ?>

        <div class="field">
        <?php
            echo form::label("$field", $field);
            echo form::input("$field",$value,'disabled=disabled');
        ?>
        </div>

    <?php endforeach; ?>



    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Done'); ?>
    </div>

    <?php echo form::close(); ?>


</div>
