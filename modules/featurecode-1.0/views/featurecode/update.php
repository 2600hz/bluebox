<div id="feature_code_update_header" class="update feature_code module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="feature_code_add_form" class="txt-left form feature_code add">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Feature Code Details'); ?>

        <div class="field">
            <?php echo form::label('featurecode[name]', 'Name:'); ?>
            <?php echo form::input(array('name' => 'featurecode[name]')); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[description]', 'Description:'); ?>
            <?php echo form::input(array('name' => 'featurecode[description]')); ?>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('FreeSWITCH XML'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label('featurecode[xml]', 'XML:');
            echo form::textarea('featurecode[xml]');
        ?>
        </div>

    <?php echo form::close_fieldset(); ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
