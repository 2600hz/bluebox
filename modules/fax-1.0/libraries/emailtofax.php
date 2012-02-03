#!/usr/bin/php
<?php
/* Script to read an email from your MTA, convert it to a tiff, and send it through FS txfax
 * Dependencies:
 * Pear::Console_Getopt
 * emailto/emailtotiff
 * PHP pecl_HTTP
 */
require_once 'Console/Getopt.php';
require_once 'convertto/emailtotiff.php';

# Print usage information
function printusage($errmsg = '')
{
	if ($errmsg != '')
		echo	'ERROR: ' . $errmsg . "\n\n"; 
		echo	"emailtofax converts an email (and attachments) to a tif file and then faxes it using the blue.box fax module. ".
				"You may also specify a cover file to include if you need to send a disclaimer " . 
				"or instructions, etc. with each fax.\n\n" . 
				"The following attachment types are supported:\n" .
				"	plain/txt, rtf, html\n" .
				"	gif, jpg, png, tif\n" .
				"	opendoc(ODF) (odt, ods, odp, ott, ots, otp)\n" .
				"	MS Office (doc, docx, dot, dotx, ppt, pot, potx, pptx, xls, xlsx)\n" .
				"	OpenOffice (stc, sti, stw, sxc, sxi, sxw)\n" .
				" 	Flash (swf)\n" .
				"	PDF (pdf)\n\n" .       
				"Parameters:\n" .
				"	-c filename		Include a cover page\n" .
				"	-d dir			Fax storage directory (defaults to \"/tmp\")\n" .
				"	-s email		System email to send error messages\n" .
				"	-h host			Host name of blue.box system (defaults to \"localhost/bluebox\")\n" .
				"	-e 				Do not send a status email to the sender (not completely implemented yet)\n\n" .
				"Usage:\n" .
				"	emailtofax [-c filename] [-d dir] [-s email] [-h host] [-e] EMAILFILE\n\n" .
				"Notes:\n" .
				"	If a dash (-) is specified as the EMAILFILE then STDIN will be used.\n";
	exit(1);		 
}

try {
	$bbhost = 'localhost/bluebox/';
	$faxstoragedir = '/tmp';
	$systememail = '';
	$statusemail = true;
	$coverfile = '';
	$infile = '';
	$outfile = '';
	$force = false;
	
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
			case '-c':
				$coverfile = $args[$i+1];
				$i++;
				break;
			case '-e':
				$statusemail = false;
				break;
			case '-d':
				$faxstoragedir = $args[$i+1];
				$i++;
				break;
			case '-h':
				$bbhost = $args[$i+1];
				$i++;
				break;
			case '-s':
				$systememail = $args[$i+1];
				$i++;
				break;
			case '-':
				$infile = 'php://stdin';
				break;
			default:
				$infile = $args[$i];
				break;
		}
	}
	
	if ($infile == '')
		printusage('No input file specified.');
	
	# Since we can only read STDIN once, we need to read the email into a temporary file for processing
	# Generate an intermediate file name for the pdf file
	$intmpname = (string) (abs((int)(microtime(true) * 100000)));
		
	# Create a temporary file name for the pdf and tiff file
	$emailfile = sys_get_temp_dir() . '/' . $intmpname . '.tmp';
	$tifffile = $faxstoragedir . '/' . $intmpname . '.tiff';
		
	# Write the intermediate file to the final destination
	$intfileres = fopen($infile, 'rb');
	$outfileres = fopen($emailfile, 'ab');
	while (!feof($intfileres))
		fwrite($outfileres, fread($intfileres, 8192));
		
	fclose($intfileres);
	fclose($outfileres);
	
	# Get the sender information for status updates
	preg_match('/^from:(.+)$/im', file_get_contents($emailfile), $fromheader);
	$sender = $fromheader[1];
	
	# Now, convert the email
	emailtotiff::processemail($emailfile, $tifffile, $coverfile);
	
	# Get the information we need for source and destination from the email
	preg_match('/^to:(.+)$/im', file_get_contents($emailfile), $toheader);
	
	$toadd = trim($toheader[1]);
	
	if (!$startpos = strpos(trim($toadd), '<'))
		$startpos = 0;
	
	if (!$endpos = strpos($toadd, '@'))
		$endpos = strlen($toadd) - $startpos;
	
	$dialstring = substr($toadd, $startpos, $endpos);
	
	# Make sure that freeswitch can read the tiff
	chmod($tifffile, 0666);
	
	# Ask Bluebox to send the fax
	$response = http_get('http://' . $bbhost . 'index.php/fax/sendfax/' . urlencode($dialstring) . '/' . urlencode(basename($tifffile)), null, $info);
	
	if (!$response)
	{
		if ($statusemail)
			mail($sender, 'Error sending fax to ' . $dialstring, print_r($info, true));

		if ($systememail != '')
			mail($systememail, 'Error sending fax to ' . $dialstring, print_r($info, true));
	}
	else
		if ($statusemail)
			mail($sender, 'Status of fax to ' . $dialstring, print_r($response, true));
} catch (Exception $e) {
	# Get the sender from the email

	if ($systememail != '')
		mail($systememail, 'Error sending fax', $e->getMessage());

	if ($statusemail)
	{
		if ($email_file === '' || !file_exists($emailfile))
			preg_match('/^from:(.+)$/im', file_get_contents($emailfile), $fromheader);

		if (count($fromheader))
			mail($fromheader[1], 'Error sending fax', $e->getMessage());
	}
}

# Remove the email file
if (isset($emailfile) && $emailfile != '' && file_exists($emailfile))
{
	unlink($emailfile);
}
	
# Remove the tiff file
if (isset($tifffile) && $tifffile != '' && file_exists($tifffile))
{
	unlink($tifffile);
}
?>