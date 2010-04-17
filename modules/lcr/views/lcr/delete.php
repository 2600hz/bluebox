<?php message::render(); ?>

<div id="lcr_delete_form" class="form delete lcr">
<?php
    echo form::open();

    echo form::open_fieldset();
    echo form::legend('Confirm Deletion');

    $name = !empty($name) ? ' ' . $name : '';
    i18n('Are you sure you want to delete %1$s?', $name)->sprintf()->e();

    echo form::close_fieldset();


    echo form::open_fieldset(array('class' => 'buttons'));

    echo form::submit('no', 'No');
    echo form::submit('confirm', 'Yes');

    echo form::close_fieldset();

    echo form::close();
?>
</div>