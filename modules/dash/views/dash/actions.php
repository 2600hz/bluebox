<p id="quickActions">

    <a href="#" id="call_link" class="dialog_link ui-widget ui-state-default ui-corner-left">
        <span class="ui-icon ui-icon-person"></span>
        <?php echo __('Call'); ?>
    </a>
    
    <a href="#" id="mobile_link" class="dialog_link ui-widget ui-state-default ui-corner-right">
        <span class="ui-icon ui-icon-document"></span>
        <?php echo __('Text'); ?>
    </a>
</p>

<?php jquery::addPlugin('dialog'); ?>
<?php jquery::addPlugin('growl'); ?>

<?php javascript::codeBlock(); ?>
    $('.dialog').dialog({ autoOpen: false, closeOnEscape: true, modal : true, resizable : false, draggable: false });

    $('#callDialog').dialog('option', 'title', '<?php echo __('Make a call'); ?>');
    $('#call_link').click(function (e) {
        e.preventDefault();
        $('#callDialog').dialog('open');
    });
    
    $('#mobileDialog').dialog('option', 'title', '<?php echo __('Send a text'); ?>');
    $('#mobile_link').click(function (e) {
        e.preventDefault();
        $('#mobileDialog').dialog('open');
    });
<?php javascript::blockEnd(); ?>