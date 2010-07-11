
    <?php echo form::open_section('Softswitch Selection'); ?>

        <div class="field">
        <?php
            echo form::label('tel_driver', 'Telephony Driver:');
            echo form::dropdown('tel_driver', $drivers, $driver);
        ?>
        </div>

    <?php echo form::close_section(); ?>



<?php
    // If the jquery exists use it to make it more interactive
    if (class_exists('jquery') )
    {
        jquery::addPlugin('blockUI');
        jquery::addQuery('')->ajaxStop('$.unblockUI');
        jquery::addQuery('#tel_driver')->change('
            function () {
                $.blockUI({ message: \'<h2>' .__('Please Wait...') .'</h2>\' });
                $(\'#installWizard\').submit();
                return true;
            }
        ');
    }
?>