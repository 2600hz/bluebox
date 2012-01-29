<?php

class filetopdf
{
	private static $driver_paths = array (
		'LOCONVERT' => '/usr/bin/loffice ',
		'CONVERT' => '/usr/bin/convert',
		'CAT' => '/bin/cat',
		'GS' => '/usr/bin/gs',
		'WKHTMLTOPDF' => '/bin/wkhtmltopdf',
		'GREP' => '/usr/bin/grep',
		'AWK' => '/usr/bin/awk',
		'RM' => '/bin/rm',
		'ECHO' => '/bin/echo',
		'CHAINCHAR' => '&&',
		'PIPECHAR' => '|'
	);
	
	private static $mime_types = array (
		#basics
		'text/plain' => array('txt' ,'loconvert'),
		'text/html' => array('htm' ,'wkhtmltopdf'),
		'application/rtf' => array('rtf' ,'loconvert'),
		
		#images
		'image/gif' => array('gif' ,'convert'),
		'image/jpeg' => array('jpg' ,'convert'),
		'image/png' => array('png' ,'convert'),
		'image/tiff' => array('tif' ,'convert'),
	
		#OpenDocument
		'vnd.oasis.opendocument.chart' => array('odc' ,'loconvert'),
		'vnd.oasis.opendocument.chart-template' => array('otc' ,'loconvert'),
		'vnd.oasis.opendocument.formula' => array('odf' ,'loconvert'),
		'vnd.oasis.opendocument.formula-template' => array('otf' ,'loconvert'),
		'vnd.oasis.opendocument.graphics' => array('odg' ,'loconvert'),
		'vnd.oasis.opendocument.graphics-template' => array('otg' ,'loconvert'),
		'vnd.oasis.opendocument.image' => array('odi' ,'loconvert'),
		'vnd.oasis.opendocument.image-template' => array('oti' ,'loconvert'),
		'vnd.oasis.opendocument.presentation' => array('opd' ,'loconvert'),
		'vnd.oasis.opendocument.presentation-template' => array('otp' ,'loconvert'),
		'vnd.oasis.opendocument.spreadsheet' => array('ods' ,'loconvert'),
		'vnd.oasis.opendocument.spreadsheet-template' => array('ots' ,'loconvert'),
		'vnd.oasis.opendocument.text' => array('odt' ,'loconvert'),
		'vnd.oasis.opendocument.text-master' => array('odm' ,'loconvert'),
		'vnd.oasis.opendocument.text-template' => array('ott' ,'loconvert'),
		'vnd.oasis.opendocument.text-web' => array('oth' ,'loconvert'),
	
	
		#MS office
		'application/vnd.ms-excel' => array('xls' ,'loconvert'),
		'application/msexcel' => array('xls' ,'loconvert'),
		'application/x-msexcel' => array('xls' ,'loconvert'),
		'application/vnd.ms-powerpoint' => array('ppt' ,'loconvert'),
		'application/vnd.ms-word' => array('doc' ,'loconvert'),
		'application/msword' => array('doc' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('docx' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => array('dotx' ,'loconvert'),
		'application/vnd.ms-word.document.macroEnabled.12' => array('docm' ,'loconvert'),
		'application/vnd.ms-word.template.macroEnabled.12' => array('dotm' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('xlsx' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => array('xltx' ,'loconvert'),
		'application/vnd.ms-excel.sheet.macroEnabled.12' => array('xlsm' ,'loconvert'),
		'application/vnd.ms-excel.template.macroEnabled.12' => array('xltm' ,'loconvert'),
		'application/vnd.ms-excel.addin.macroEnabled.12' => array('xlam' ,'loconvert'),
		'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => array('xlsb' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('pptx' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.presentationml.template' => array('potx' ,'loconvert'),
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => array('ppsx' ,'loconvert'),
		'application/vnd.ms-powerpoint.addin.macroEnabled.12' => array('ppam' ,'loconvert'),
		'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => array('pptm' ,'loconvert'),
		'application/vnd.ms-powerpoint.template.macroEnabled.12' => array('potm' ,'loconvert'),
		'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => array('ppsm' ,'loconvert'),
	
		#PDF
		'application/pdf' => array('pdf', 'pdf')
	);
	
	private static $mime_drivers = array (
		'loconvert' => 'LOCONVERT --headless --convert-to pdf -outdir OUTDIR INFILE PIPECHAR GREP convert PIPECHAR AWK \'{print $4}\' CHAINCHAR RM INFILE',
		'convert' => 'CONVERT INFILE pdf:INFILE.pdf CHAINCHAR RM INFILE CHAINCHAR ECHO INFILE.pdf',
		'pdf' => 'ECHO INFILE',
		'gs' => 'GS -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=INTFILE COMBLIST',
		'wkhtmltopdf' => 'WKHTMLTOPDF INFILE INFILE.pdf > /dev/null 2>&1 CHAINCHAR RM INFILE CHAINCHAR ECHO INFILE.pdf'
	);
	
	public static function processfile($infilename, $type = null)
	{
		if (!$type)
			$type = mime_content_type($infilename);
		
		#build and execute the conversion command
		$convcom = str_replace(array_merge(array('INFILE', 'OUTDIR'), array_keys(self::$driver_paths)), array_merge(array($infilename, sys_get_temp_dir()), array_values(self::$driver_paths)), self::$mime_drivers[self::$mime_types[$type][1]]);
		$comret = array();
		
		exec($convcom, $comret);
		return $comret[0];
	}
}
?>
