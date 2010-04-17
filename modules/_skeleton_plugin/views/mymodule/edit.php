<h4>MyPlugin</h4>
<div>
    <table cellspacing=0 cellpadding=3>
        <tr height="30">
            <td>My Data Field #1</td>
            <td colspan="5"><?php echo form::input('myplugin[mydatafield1]', $myplugin['mydatafield1'], 'style="width:300px" class="text"'); ?><br><?= error::form('myplugin[mydatafield1]'); ?></td>
        </tr>
        <tr height="30">
            <td>My Data Field #2</td>
            <td colspan="5"><?php echo form::input('myplugin[mydatafield2]', $myplugin['mydatafield2'], 'style="width:300px" class="text"'); ?><br><?= error::form('myplugin[mydatafield2]'); ?></td>
        </tr>
    </table>
</div>
