<div id="callDialog" class="dialog">
	<?php echo form::open('dash/call', array('id' => 'dashCallForm'));?>

        <div class="field">
        <?php
            echo form::label('searchCallable', 'Number');
            echo form::input(array('name' => 'to', 'id' => 'searchCallable'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number_id', 'Call From');
            echo form::dropdown('number_id',  sipdevices::getSipEndpoints() );
        ?>
        </div>

        <div class="buttons">
            <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Call'); ?>
        </div>
	<?php echo form::close();?>
</div>

<div id="mobileDialog" class="dialog">
	<?php echo form::open('dash/mobile', array('id' => 'dashMobileForm'));?>

        <div class="field">
        <?php
            echo form::label('dashSearchMobile', 'Number (mobile only)');
            echo form::input(array('name' => 'to', 'id' => 'dashSearchMobile'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('message', 'Message (160)');
            echo form::textarea('message');
        ?>
        </div>

        <div class="buttons">
            <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Send'); ?>
        </div>
    
	<?php echo form::close();?>
</div>

<?php jquery::addPlugin('form'); ?>

<?php javascript::codeBlock(); ?>
    var options = {
        clearForm : true,
        beforeSubmit: dashSubmit,
        success: dashResponse
    };
    
    $('#dashCallForm').ajaxForm(options);
    $('#dashMobileForm').ajaxForm(options);

    $("#searchCallable").autocomplete('<?php echo url::site('dash/searchCallable');?>');
    $("#searchMobile").autocomplete('<?php echo url::site('dash/searchMobile');?>');

    function dashSubmit(formData, jqForm, options) {
        $('.dialog').dialog('close');
    }

    function dashResponse(responseText, statusText) {
        $.jGrowl(responseText, { life : 4000 });
    }
<?php javascript::blockEnd(); ?>

