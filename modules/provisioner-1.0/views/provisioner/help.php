<h2 class="txt-center">Provisioning Help</h2>
<ul>
	<li>Setting up your server</li>
	<li>FTP</li>
	<li>TFTP</li>
</ul>

<h3>Setting up your server</h3>
<p>Linux users need to setup sudo and nmap in order for scanning to correctly work.  If you downloaded the official ISO, you don't have to worry!</p>
<p>After installing sudo and nmap using your package manager you must add the following lines to your /etc/sudoers file.  Don't edit the file directly, use the command visudo as root.  Carefully add the following to the end of the file:</p>
<p>&nbsp;</p>
<pre>
	Cmnd_Alias FREESWITCH_CMDS = /usr/bin/nmap
	www-data ALL=NOPASSWD: FREESWITCH_CMDS
</pre>
<p>If your /etc/sudoers file containes the following directive you must comment it out as well.</p>
<p>&nbsp;</p>
<pre>
    # Defaults    requiretty
</pre>
<p>&nbsp;</p>
<p>This will allow the web service to run nmap and nmap only for the purpose of scanning.  Nmap needs to run at an elevated security level in order to probe your network.</p>
<h3>FTP</h3>
<p>The officially supported FTP daemon of the Bluebox project is <a href="http://www.proftpd.org/">ProFTPD</a>.  With the official Bluebox distribution you will find that all your sip devices's logins and passwords are also FTP credentials to the provision evironment for each phone. There are two modes for running FTP.  1.) each account is given it's own private directory (private mode) and 2.) public mode in which all files are in a shared directory.</p>
<h2>TFTP</h2>
TFTP is a very common way to provision phones. It's use it however discourges.  Although not offically supported it easy to use.  Your provision configuration settings can be set to:
<pre>
$config['directory'] = '/tftpboot/';
</pre>
This is the stardard on most Linux system, and atftpd will default to this on Debian based distros.
<ul>
	<li>It uses UDP port 69 as its transport protocol (unlike FTP which uses TCP port 21).</li>
    <li>It cannot list directory contents.</li>
    <li>It has no authentication or encryption mechanisms.</li>
    <li>It is used to read files from, or write files to, a remote server.</li>
    <li>Due to the lack of security, it is dangerous over the open Internet. Thus, TFTP is generally only used on private, local networks.</li>
</ul>
