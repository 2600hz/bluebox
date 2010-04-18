<div id="permission_update_header" class="update permission module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="permission_update_form" class="update permission">
    <?php echo form::open(); ?>

    <div id="permissionTreeContainter">
        <ul id="permissionTree" class="filetree">

        </ul>
    </div>

    <div id="permissionForm" style="overflow: auto;">Please select a user</div>


    <div style="clear:both;">&nbsp;</div>
    
    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>

<?php jquery::addPlugin(array('treeview', 'blockUI')); ?>

<?php javascript::codeBlock(); ?>
    $('#permissionTree').treeview({ 
        url: '<?php echo url::site('permission/tree'); ?>'
    });
    
    $(document).ajaxStop(function () {
        $('.user').unbind('click').click(function (e) {
            e.preventDefault();
            $.post('<?php echo url::site('permission/permissions'); ?>', {
                'root':$(this).parent().attr('id')
            },
            function (data) { $('#permissionForm').html(data); }, 'html');
        });
    });
    
    $(document).ajaxStart(function () {
        $.blockUI()
        }).ajaxStop($.unblockUI);
<?php javascript::blockEnd(); ?>
