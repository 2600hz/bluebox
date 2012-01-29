#!/usr/bin/php
<?php
/* Send files to destination as email attachments
 * Dependencies:
 * Pear::Console_Getopt
 * Pear::Mail
 * Pear::Mail_MIME
 */
require_once 'Console/Getopt.php';
require_once 'Mail.php';
require_once 'Mail/mime.php';
require_once '../convertto/filetopdf.php';

# Print usage information
function printusage($errmsg = '')
{
	if ($errmsg != '')
		echo	'ERROR: ' . $errmsg . "\n\n"; 
		echo	"sendasemail attaches one or more files to an email sends it to a specified address or sends an error email to the ".
				"system administrator email (if specified) and to the destination email if it cannot be sent.\n\n" . 
				"Parameters:\n" .
				"	-s email		System email to send error messages\n" .
				"	-f email		From Address\n" .
				"	-n text			From Name\n" .
				"	-d email		Destination Address\n" .
				"	-u text			Email Subject\n" .
				"	-b text			Email Body\n" .
				"	-r text			Fax Result Code\n" .
				"	-t text			Fax Result Text\n" .
				"	-i text			Fax Remote Station Ident\n" .
				"	-ocidname text	Caller ID name for sending fax\n" .
				"	-ocidnum text	Caller ID number for sending fax\n" .
				"	-pr text		Number of pages received\n" .
				"	-pt text		Total number of pages\n" .
				"	-pdf			Convert FILES to PDF before attaching them\n" .
				"	-noclean		Do not clean up files\n" .
				"	-e			Do not send a status email to the sender (not completely implemented yet)\n\n" .
				"Usage:\n" .
				"	emailtofax [-e] [-s email] [-u \"subject\"] [-b \"body\"] [-n \"fromname\"] -f email -d email FILELIST\n\n" .
				"Notes:\n" .
				"	Files should be separated by spaces.\n" .
				"	If a dash (-) is specified as the FILELIST then STDIN will be used.\n";
	exit(1);		 
}

try {
	$cleanup = true;
	$systememail = '';
	$statusemail = true;
	$fromemail = '';
	$fromname = '';
	$destemail = '';
	$emailsubject = '';
	$emailbody = '';
	$filelist = array();
	$resultcode = 0;
	$resulttext = '';
	$remoteident = '';
	$remotecidname = '';
	$remotecidnum = '';
	$pagesreceived = 0;
	$totalpages = 0;
	$topdf = false;
	
	$cg = new Console_Getopt();
	$args = $cg->readPHPArgv();
	array_shift($args);
	
	for ($i=0; $i<count($args); $i++)
	{
		switch ($args[$i])
		{
			case '--help':
			case '-h':
			case '-?':
				printusage('');
				break;
			case '-noclean':
				$cleanup = false;
				break;
			case '-e':
				$statusemail = false;
				break;
			case '-f':
				$fromemail = $args[$i+1];
				$i++;
				break;
			case '-n':
				$fromname = $args[$i+1];
				$i++;
				break;
			case '-s':
				$systememail = $args[$i+1];
				$i++;
				break;
			case '-d':
				$destemail = $args[$i+1];
				$i++;
				break;
			case '-u':
				$emailsubject = $args[$i+1];
				$i++;
				break;				
			case '-b':
				$emailbody = $args[$i+1];
				$i++;
				break;
			case '-r':
				$resultcode = $args[$i+1];
				$i++;
				break;
			case '-t':
				$resulttext = $args[$i+1];
				$i++;
				break;
			case '-i':
				$remoteident = $args[$i+1];
				$i++;
				break;
			case '-ocidname':
				$remotecidname = $args[$i+1];
				$i++;
				break;
			case '-ocidnum':
				$remotecidnum = $args[$i+1];
				$i++;
				break;
			case '-pr':
				$pagesreceived = $args[$i+1];
				$i++;
				break;
			case '-pt':
				$totalpages = $args[$i+1];
				$i++;
				break;
			case '-pdf':
				$topdf = true;
				break;
			case '-':
				$filelist[] = '-';
				break;
			default:
				$filelist[] = $args[$i];
				break;
		}
	}
	
	if ($destemail === '')
		printusage('No destination email specified.');
	
	if ($fromemail === '')
		$fromemail .= 'fax@' . php_uname('n');
		
	if ($fromname === '')
		$fromname = 'Fax System';

	if ($emailsubject === '')
		$emailsubject = 'Fax Received from ' . $remoteident . ' (' . $remotecidname . '/' . $remotecidnum . ')';
		
	if ($emailbody !== '')
	{
		if (strstr('<', $emailbody))
		{
			$emailhtml = $emailbody;
			$emailbody = strip_tags(str_replace(array('<br>', '</p>', '<br/>', '<br />'), "/n", $emailbody));
		}
		else
			$emailhtml = '<html><body>' . $emailbody . '</body></html>';
	}
	else
	{
		$emailbody = 'Fax Received from ' . $remoteident . ' (' . $remotecidname . '/' . $remotecidnum . ").\n" .
			$totalpages . ' were sent, and ' . $pagesreceived . "were received. \n" . 
			'The result code was ' . $resultcode . ' and message was ' . $resulttext;
		$emailbody = '<html><body>Fax Received from ' . $remoteident . ' (' . $remotecidname . '/' . $remotecidnum . ").<br />" .
			$totalpages . ' were sent, and ' . $pagesreceived . "were received.<br />" . 
			'The result code was ' . $resultcode . ' and message was ' . $resulttext . '</body></html>';
	}
		
	$hdrs = array(
		'From'    => $fromemail,
		'Subject' => $emailsubject
	);

	$mime = new Mail_mime();

	$mime->setTXTBody($emailbody);
	$mime->setHTMLBody($emailhtml);
	
	foreach ($filelist as $curfile)
	{
		if ($topdf && mime_content_type($curfile) != 'application/pdf')
		{
			filetopdf::processfile($curfile);
			$curfile .= '.pdf';
		}
		
		$mime->addAttachment($curfile, mime_content_type($curfile));
	}

	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);

	$mail =& Mail::factory('mail');
	$mail->send($destemail, $hdrs, $body);
	
	//Clean up after ourselves
	if ($cleanup)
		foreach ($filelist as $curfile)
		{
			unlink($curfile);
			if (substr($curfile, -4) != '.pdf')
			{
				unlink($curfile . '.pdf');
			}
		}
	
	exit();
} catch (Exception $e) {
	if ($systememail !== '')
		mail($systememail, 'Error sending email: ' . $emailsubject, $e->getMessage() . "\nWith Files: " . print_r($filelist, true));
	if ($statusemail)
		mail($systememail, 'Error sending email: ' . $emailsubject, $e->getMessage() . "\nWith Files: " . print_r($filelist, true));
	exit(1);
}
