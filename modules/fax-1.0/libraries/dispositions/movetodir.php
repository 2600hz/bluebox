#!/usr/bin/php
<?php
/* Script to move a file from one to another
 * Dependencies:
 * Pear::Console_Getopt
 */
require_once 'Console/Getopt.php';

# Print usage information
function printusage($errmsg = '')
{
	if ($errmsg != '')
		echo	'ERROR: ' . $errmsg . "\n\n"; 
		echo	"movetodir simply moves a file from one directory to another, and sends an email to a ".
				"specified system administrator email if the move is not successful.\n\n" . 
				"Parameters:\n" .
				"	-r text			Fax Result Code\n" .
				"	-t text			Fax Result Text\n" .
				"	-i text			Fax Remote Station Ident\n" .
				"	-pr text		Number of pages received\n" .
				"	-pt text		Total number of pages\n".
				"	-n email		Email to send receipt notifications to (success or errors)\n" .
				"	-s email		System email to send error messages (only script errors, not transmission errors)\n\n" .
				"Usage:\n" .
				"	emailtofax [-s email] SOURCEFILE DESTFILE\n\n" .
				"Notes:\n" .
				"	If a dash (-) is specified as the SOURCEFILE or DESTFILE then STDIN and STDOUT will be used respectively.\n";
	exit(1);		 
}

try {
	$systememail = '';
	$statusemail = true;
	$infile = '';
	$outfile = '';
	
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
			case '-s':
				$systememail = $args[$i+1];
				$i++;
				break;
			case '-':
				if ($infile === '')
					$infile = 'php://stdin';
				else
					$outfile = 'php://stdout';
				break;
			default:
				if ($infile === '')
					$infile = $args[$i];
				else
					$outfile = $args[$i];
				break;
		}
	}
	
	if ($infile === '')
		printusage('No input file specified.');
		
	if ($outfile === '')
		printusage('No output file specified.');
		
	$intfileres = fopen($infile, 'rb');
	$outfileres = fopen($outfile, 'ab');
	while (!feof($intfileres))
		fwrite($outfileres, fread($intfileres, 8192));
		
	fclose($intfileres);
	fclose($outfileres);
	
	# Remove the original file
	unlink($infile);		
	
	exit();

} catch (Exception $e) {
	mail($systememail, 'Error moving file', $e->getMessage());
	exit(1);
}
