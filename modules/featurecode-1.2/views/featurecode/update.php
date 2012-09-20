<!-- start here, see phpdoc style comments in  bb/libraries/drivers/telelphony.php -->

<div id="feature_code_update_header" class="update feature_code module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="feature_code_add_form" class="txt-left form feature_code add">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Feature Code Details'); ?>

        <div class="field">
            <?php echo form::label('featurecode[name]', 'Name:'); ?>
            <?php echo form::input('featurecode[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[description]', 'Description:'); ?>
            <?php echo form::input('featurecode[description]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[custom_feature_code_id]', 'Feature:'); ?>
            <?php echo form::dropdown('featurecode[custom_feature_code_id]', $featurecodes); ?>
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
