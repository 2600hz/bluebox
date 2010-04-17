    <?php echo form::open_section('Installation Complete!'); ?>

    <div class="finalize">
        <?php echo html::anchor('/welcome', 'Click here to use FreePBX ' .FreePbx_Controller::$version .'!'); ?>
    </div>

    <?php echo form::close_section(); ?>