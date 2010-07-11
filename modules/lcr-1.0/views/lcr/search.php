<div id="lcr_add_header" class="txt-center add lcr tab_header">
    <h2><?php echo __(Router::$method .' an LCR Entry'); ?></h2>
</div>
<script type="text/javascript">
    $(function() {
        $("#lcr_valid_date").datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>


<?php message::render(); ?>

<div id="lcr_add_form" class="txt-left form add lcr">
    <?php
    jquery::addPlugin('datepicker');
    echo form::open();
    echo form::open_fieldset();
    echo form::legend('LCR Search');

    echo form::label('lcr[digits]', 'LCR Digits:');
    echo form::input('lcr[digits]', null, 'class="text"');

    echo form::label('lcr[lcr_profile]', 'LCR Group');
    echo form::input('lcr[lcr_profile]', null, 'class="text"');

    echo form::label('lcr[valid_date', 'Entries Valid on Date (optional)');
    echo form::input('lcr[valid_date]', null, 'class="text"');


    echo form::submit('submit', 'Save');
    echo form::close_fieldset();
    echo form::close();
    ?>
</div>

<?php if(!empty($grid)) { echo $grid; } ?>