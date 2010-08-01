<!-- start here, see phpdoc style comments in  bb/libraries/drivers/telelphony.php -->
<?php
    $registry = $featurecode['registry'];
?>

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

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views, array('number_inventory'));
        }
    ?>

    <?php echo form::open_section('XML Sections'); ?>

    <?php foreach ( FreeSwitch::getDialplanSections() as $section ): ?>

        <div id="<?php echo $section; ?>-xml" class="used section-<?php echo ( isset($registry[$section]) ? '' : 'no' ) ?>xml">

          <div>

            <span><?php echo ucfirst($section) ?></span>

            <span class="clear-section">&mdash;</span>

          </div>

<textarea class="section-editor" id="featurecode_registry_<?php echo $section; ?>" name="featurecode[registry][<?php echo $section; ?>]">
<?php echo (isset($registry[$section]) ? $registry[$section] : ''); ?>
</textarea>

        </div>

    <?php endforeach; ?>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Add XML Sections'); ?>

    <?php foreach ( FreeSwitch::getDialplanSections() as $section): ?>

        <div id="<?php echo $section; ?>-empty" class="unused section-<?php echo ( isset($registry[$section]) ? '' : 'un' ) ?>used">

          <span style="font-weight: bold">+</span>

          <span><?php echo ucfirst($section) ?></span>

        </div>

    <?php endforeach; ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>