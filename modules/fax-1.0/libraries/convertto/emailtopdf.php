<?php
/* Email to pdf converter class
 * 
 * Dependencies:
 * Pear::Mail_mimeDecode
 * php Fileinfo
 * 
 * ghostscript
 * imagemagick
 * libreoffice
 * wkhtmltopdf
 * 
 * awk
 * grep
 * echo
 * 
 */

require_once 'Mail/mimeDecode.php';
require_once 'filetopdf.php';

class emailtopdf
{
	public static function processpart($body, $type)
	{
		# build a temp file name for processing the part into
		$intmpname = (string) (abs((int)(microtime(true) * 100000)));
		
		# put the data into the input file for conversion
		$srcext = filetopdf::$mime_types[$type][0];
		$infilename = sys_get_temp_dir() . '/' . $intmpname . '.'. $srcext;
		file_put_contents($infilename, $body);

		return self::processfile($infilename, $type);
	}
	
	public static function processparts($emailparts)
	{
		//print_r($emailparts);
		$combfilelist = array();
		
		foreach ($emailparts->parts as $partstruc)
		{
			if (strtolower($partstruc->ctype_primary) === 'multipart')
			{
				$combfilelist = array_merge($combfilelist, processparts($partstruc));
				continue;
			} 
			
			$parttype = $partstruc->ctype_primary . '/' . $partstruc->ctype_secondary;
			$combfilelist[] = self::processpart($partstruc->body, $parttype);
		}
		return $combfilelist;
	}

	public static function processemail($emailsrc, $pdfout, $coverfile = '')
	{
		$combfilelist = array();
		
		# Process the email
		$emailparts = Mail_mimeDecode::decode(
				array(
					'include_bodies' => true,
					'decode_bodies' => true,
					'decode_headers' => true,
					'input' => file_get_contents($emailsrc),
					'crlf' => "\r\n"
				)
		);
		
		# Process the cover if it exists
		if ($coverfile !== '')
		{
			$combfilelist[] = self::processpart(file_get_contents($coverfile), mime_content_type($coverfile));
		}
		
		# Process the parts
		$combfilelist = array_merge($combfilelist, self::processparts($emailparts));
		
		# Create an intermediate file to build the pdf
		$tmppdffilename = sys_get_temp_dir() . '/e2p-' . (string) (abs((int)(microtime(true) * 100000))) . '.pdf';
		
		# Build the command to combine all of the intermediate files into one
		$conbcom = str_replace(array_merge(array('INTFILE', 'COMBLIST'), array_keys(self::$driver_paths)), array_merge(array($tmppdffilename, implode(' ', $combfilelist)), array_values(self::$driver_paths)), self::$mime_drivers['gs']);
		exec($conbcom);
		
		# Remove the intermediate files
		foreach ($combfilelist as $combfilename)
			unlink ($combfilename);		
		
		# Write the intermediate file to the final destination
		$intfileres = fopen($tmppdffilename, 'rb');
		$outfileres = fopen($pdfout, 'ab');
		while (!feof($intfileres))
			fwrite($outfileres, fread($intfileres, 8192));
			
		fclose($intfileres);
		fclose($outfileres);
		
		# Remove the intermediate file
		unlink($tmppdffilename);
	}
}
?>