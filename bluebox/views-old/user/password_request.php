<?php message::render(); ?>
<table align="center">
    <tr>
        <td align="left">
            <?php echo form::open(NULL, array('name' => 'form'), array('action' => 'submit')); ?>
            <table >
                <tr valign="top">
                    <td colspan="2">
                        <B>Password Reset Request</B><BR>
                        You are requesting a reset of your password. Please enter your email address and click <i>Remind Me!</i><BR>
                        A link will be sent to your email address which, when clicked, will allow you to reset your password.<BR>
                        <BR>
                    </td>
                </tr>
                <tr style="height:3px;"><td colspan="3"></td></tr>
                <tr valign="top">
                    <td width="100"><B>Email Address: </B></td>
                    <td>
                        <?php echo form::input('email_address', $form['email_address'], 'style="width:130px" class="searchField"'); ?><BR>
                        <?php echo $errors['email_address']; ?>
                    </td>
                </tr>
                <tr style="height:3px;"><td colspan="3"></td></tr>
                <tr valign="top">
                    <td>&nbsp;</td>
                    <td>
                        <table width="91" border="0" cellpadding="0" cellspacing="0">
                            <tr valign="top">
                                <td><button>Remind Me!</button></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo form::close(); ?>
        </td>
    </tr>
</table>
