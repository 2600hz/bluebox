<div id="lcr_edit_header" class="txt-center edit lcr tab_header">
    <h2><?php echo __(Router::$method .' an LCR Entry'); ?></h2>
</div>

<script type="text/javascript">
    $(function() {
        $("#lcr_date_start").datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#lcr_date_end").datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>

<?php message::render(); ?>

<div id="lcr_edit_form" class="txt-left form edit lcr">
    <?php
    jquery::addPlugin('datepicker');
    echo form::open();
    echo form::open_fieldset();
    echo form::legend('LCR Entry');
    echo html::br();

    echo form::label('lcr[digits]', 'Digits');
    echo form::input('lcr[digits]', $Lcr['digits'], 'class="text"');

    echo html::br();

    echo form::label('lcr[carrier_id]', 'Carrier');
    echo form::dropdown('lcr[carrier_id]', $carriers, $Lcr['carrier_id']);
    echo html::br();
/*
    echo form::label('lcr[lcr_profile]', 'LCR Group');
    echo form::input('lcr[lcr_profile]', null, 'class="text"');
    echo html::br();
*/
    echo form::label('lcr[rate]', 'Interstate Rate');
    echo form::input('lcr[rate]', $Lcr['rate'], 'class="text"');
    echo html::br();

    echo form::label('lcr[intrastate_rate]', 'Intrastate Rate');
    echo form::input('lcr[intrastate_rate]', $Lcr['intrastate_rate'], 'class="text"');
    echo html::br();

    echo form::label('lcr[intralata_rate]', 'Intralata Rate');
    echo form::input('lcr[intralata_rate]', $Lcr['intralata_rate'], 'class="text"');
    echo html::br();

    echo form::label('lcr[lead_strip]', '# Of Digits To Front Strip');
    echo form::input('lcr[lead_strip]', $Lcr['lead_strip'], 'class="text"');
    echo html::br();

    echo form::label('lcr[trail_strip]', '# Of Digits To End Strip');
    echo form::input('lcr[trail_strip]', $Lcr['trail_strip'], 'class="text"');
    echo html::br();

    echo form::label('lcr[prefix]', 'Prefix Digits');
    echo form::input('lcr[prefix]', $Lcr['prefix'], 'class="text"');
    echo html::br();

    echo form::label('lcr[suffix]', 'Suffix Digits');
    echo form::input('lcr[suffix]', $Lcr['suffix'], 'class="text"');
    echo html::br();

    echo form::label('lcr[date_start]', 'Valid Date Range Begin');
    echo form::input('lcr[date_start]', $Lcr['date_start'], 'class="text"');
    echo html::br();

    echo form::label('lcr[date_end', 'Valid Date Range End');
    echo form::input('lcr[date_end]', $Lcr['date_end'], 'class="text"');
    echo html::br();

    echo form::submit('submit', 'Save');
    echo form::close_fieldset();
    echo form::close();
