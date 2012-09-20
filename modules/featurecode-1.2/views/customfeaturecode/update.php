<!-- start here, see phpdoc style comments in  bb/libraries/drivers/telelphony.php -->

<div id="custom_feature_code_update_header" class="update custom_feature_code module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="custom_feature_code_add_form" class="txt-left form custom_feature_code add">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Feature Code Details'); ?>

        <div class="field">
            <?php echo form::label('customfeaturecode[name]', 'Name:'); ?>
            <?php echo form::input('customfeaturecode[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('customfeaturecode[description]', 'Description:'); ?>
            <?php echo form::input('customfeaturecode[description]'); ?>
        </div>

	<div class="field">
	    <?php echo form::label('customfeaturecode[dialplan_code]','Dialplan:'); ?>
	    <?php echo form::textarea(array('name'=>'customfeaturecode[dialplan_code]','cols'=>80,'rows'=>20)); ?>
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
