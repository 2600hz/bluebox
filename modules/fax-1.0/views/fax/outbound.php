<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<h2>SECURITY WARNING:</h2><p class="fax">Anyone who can send email to the email address you set up will be able to use your email to 
fax gateway, which can be expensive on the phone side, as well as a waste of resources.  Protect the address accordingly 
using your MTA or whatever.  You have been warned!!!</p>
<p class="fax"><em class="fax"><?php echo Kohana::config('core.product_name');?></em> allows you to set up an email to fax gateway that will send 
faxes through your softswitch.  To set this up, you create a virtual email address in your MTA and point that address at 
the emailtofax.php script located in the 
{<?php echo Kohana::config('core.product_name');?> root}/modules/fax-{ver}/libraries directory.  This script converts 
the email and attachments to the tiff format and then send the fax to the recipient.</p>
<p class="fax"><em>NOTE: The settings in your default profile are used to send faxes.</em></p>
<p class="fax">The script expects to have the outbound number delivered in the to address on the email. ex. 
5555551212@fax.yourdomain.com.  The fqdn part can be anything that you would like to use as long as the email can 
be routed to the server using your MTA.  For additional information on doing this, please see the documenatation 
for your MTA.</p>
<p class="fax">Server configuration examples for Sendmail and Postfix follow: (these instructions vary by distribution)<p>
	<ul class="faxlist">
		<li>Postfix
			<ol class="faxlist">
				<li>Create a virtual alias map by putting an entry such as "virtual_alias_maps = hash:/etc/postfix/valias" into main.cf.</li>
				<li>Create /etc/postfix/valias with an entry such as "@fax.domain.com emailtofax". Run 'postmap valias' to create 
					the valias.db file. This tells postfix to point all mail for fax.domain.com at the mail2fax alias.</li> 
				<li>Create an entry on /etc/aliases like<br><br>
				emailtofax: "|/var/www/html/bluebox/modules/fax-1.0/libraries/emailtofax.php -s support@domain.com -"<br><br>
				<em>NOTE: There is a dash at the end</em></li>
				<li>Run 'newaliases'. This tells postfix to send all mail for the alias emailtofax to the script.</li>
				<li>Reload postfix using a command like 'postfix reload'.</li>
			</ol>
			<br>
			<br>
		</li>
		<li>Sendmail
			<ol class="faxlist">
				<li>Create a "virtusertable" with an entry such as "@fax.domain.com emailtofax" in /etc/mail/virtusertable</li>
				<li>Run "make" to create the virtusertable.db file. This tells sendmail to point all mail for fax.domain.org at the emailtofax alias</li>
				<li>Create an entry in /etc/aliases like:<br><br>
				emailtofax: "|/var/www/html/bluebox/modules/fax-1.0/libraries/emailtofax.php -s support@domain.com -"<br><br>
				<i>NOTE: There is a dash at the end</i><br>
				<i>NOTE: If sendmail uses smrsh (and it probably does) then an appropriate symlink will need to be made for smrsh (probably 'ln -s var/www/html/bluebox/modules/fax-1.0/libraries/emailtofax.php /etc/smrsh/mailtofax.php')</i><br><br>
				This tells sendmail to send all mail for the alias emailtofax to the script.<br><br></li>
			</ol>
		</li>
	</ul>
<p class="fax"></p>
<p class="fax">
	There are several script options that can be customized.  These can be viewed by running the script from the command line, and
	are documented here:</p>
	<ul class="faxlist">
		<li>-c filename		Include a cover page</li>
		<li>-d dir			Fax storage directory (defaults to "/tmp")</li>
		<li>-s email		System email to send error messages</li>
		<li>-h host			Host name of blue.box system (defaults to "localhost/bluebox")</li>
		<li>-e 				Do not send a status email to the sender (not completely implemented yet)</li>
	</ul>
<p class="fax"></p>