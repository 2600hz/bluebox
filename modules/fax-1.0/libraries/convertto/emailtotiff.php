
<?php
/* Email to fax converter
 * 
 * Dependencies
 * imagemagick
 * 
 * emailto/emailtopdf
 */

require_once 'emailtopdf.php';

class emailtotiff
{
	private static $convdriver = '/usr/bin/convert -density 300 PDFFILE OUTFILE > /dev/null 2>&1';
	
	public static function processemail($infile, $outfile, $coverfile)
	{
		#Generate an intermediate file name for the pdf file
		$intmpname = (string) (abs((int)(microtime(true) * 100000)));
			
		# Create a temporary file name for the pdf and tiff file
		$pdffile = sys_get_temp_dir() . '/' . $intmpname . '.pdf';
		$tiffile = sys_get_temp_dir() . '/' . $intmpname . '.tiff';
		
		emailtopdf::processemail($infile, $pdffile, $coverfile);
		
		$convcom = str_replace(array('PDFFILE', 'OUTFILE'), array($pdffile, $tiffile), self::$convdriver);
		exec($convcom);
		
		# Write the intermediate file to the final destination
		$intfileres = fopen($tiffile, 'rb');
		$outfileres = fopen($outfile, 'ab');
		while (!feof($intfileres))
			fwrite($outfileres, fread($intfileres, 8192));
			
		fclose($intfileres);
		fclose($outfileres);
		
		# Remove the intermediate files
		unlink($pdffile);
		unlink($tiffile);
	}
}
?>


