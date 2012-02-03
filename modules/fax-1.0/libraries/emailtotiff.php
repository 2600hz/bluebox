#!/usr/bin/php
<?php
/* Command line wrapper for Email to fax converter
 * 
 * Dependencies:
 * Pear::Console_Getopt
 * 
 * emailto/emailtotiff
 */
require_once 'Console/Getopt.php';
require_once 'convertto/emailtotiff.php';

# Print usage information
function printusage($errmsg = '')
{
	if ($errmsg != '')
		echo 	'ERROR: ' . $errmsg . "\n\n"; 
		echo	"emailtotiff converts an email (and attachments) to a tiff file. ".
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
				"	-c filename		Include a cover page\n\n" .
				"Usage:\n" .
				"	emailtotiff [-c filename] EMAILFILE OUTPUTFILE\n\n" .
				"Notes:\n" .
				"	If a dash (-) is specified as the EMAILFILE or the OUTPUT FILE, then STDIN and STDOUT will be used respectively.\n" .
				"	If the EMAILFILE is specified, but the OUTPUTFILE is not, then the output will be written to INPUTFILE.pdf.\n";	
	exit(1);		 
}

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
		case '-f':
			$force = true;
			break;
		case '-':
			if ($infile == '')
				$infile = 'php://stdin';
			else
				$outfile = 'php://stdout';
			break;
		default:
			if ($infile == '')
				$infile = $args[$i];
			else
				$outfile = $args[$i];
			break;
	}
}

if ($infile == '')
	printusage('No input file specified.');


if ($outfile == '')
	if ($infile != 'php://stdin')
		$outfile = $infile . '.pdf';
	else
		printusage('No output file specified.');
				
# Check to see if the output file exists
if (file_exists($outfile))
{
	# If it exists, check to see if we are to overwrite it.
	if ($force)
		unlink($outfile);
	else
		printusage('Output file already exists.  Use -f to overwrite.');
}
touch($outfile);

emailtotiff::processemail($emailfile, $outfile, $coverfile);

?>