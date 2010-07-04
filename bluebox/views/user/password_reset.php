<h1>Check your email!</h1>
You were just sent an email containing a token for resetting your password.<BR>
<BR>
Enter the token below, along with a new password, to reset your password.<BR>
<BR>
<BR>
<table align="center">
    <tr>
        <td align="left">
            <?php echo form::open(NULL, array('name' => 'form'), array('action' => 'submit')); ?>
            <table >
                <tr valign="top">
                    <td width="100"><B>Email Address: </B></td>
                    <td>
                        <?php echo form::input('email_address', $form['email_address'], 'style="width:200px" class="searchField"'); ?><BR>
                        <?php echo $errors['email_address']; ?>
                    </td>
                </tr>
                <tr style="height:3px;"><td colspan="3"></td></tr>
                <tr valign="top">
                    <td width="100"><B>Reset Token: </B></td>
                    <td>
                        <?php echo form::input('token', $form['token'], 'style="width:200px" class="searchField"'); ?><BR>
                        <?php echo $errors['token']; ?>
                    </td>
                </tr>
                <tr style="height:3px;"><td colspan="3"></td></tr>
                <tr valign="top">
                    <td width="100"><B>New Password: </B></td>
                    <td>
                        <?php echo form::password('password', $form['password'], 'style="width:200px" class="searchField"'); ?><BR>
                        <?php echo $errors['password']; ?>
                    </td>
                </tr>
                <tr style="height:3px;"><td colspan="3"></td></tr>
                <tr valign="top">
                    <td>&nbsp;</td>
                    <td>
                        <table width="121" border="0" cellpadding="0" cellspacing="0">
                            <tr valign="top">
                            <td><button>Reset Password</button></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo form::close(); ?>
        </td>
    </tr>
</table>
