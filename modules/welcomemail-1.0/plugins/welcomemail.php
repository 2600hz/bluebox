<?php defined('SYSPATH') or die('No direct access allowed.');

class Welcomemail_Plugin extends Bluebox_Plugin
{
    protected $name = 'welcomemail';
    
    protected function viewSetup()
    {
        parent::viewSetup();

        // Load the states array to the view
        $this->subview->test = "Welcome email";

        return TRUE;
    }

    public function save()
    {
	global $email;
	if (($_REQUEST['welcomemail']['flag']==1) && ($_REQUEST['welcomemail']['emailaddress'] != '')) {
		$email=$_REQUEST;
	        $email['directory_url']="http://".preg_replace('/\/\/+/','/',
			$_SERVER['SERVER_ADDR'].Kohana::config('core.site_domain').Kohana::config('core.index_page').'/directory');
		$vmail=array_keys($_REQUEST['numbers']['assigned']);
		$vmail=$_REQUEST['number'.$vmail[0]]['dialplan']['terminate']['voicemail'];
		$email['vmail']=Doctrine::getTable('Voicemail')->find($vmail,DOCTRINE::HYDRATE_ARRAY);
		$email['DUMP']=print_r($email,TRUE);
		$file=file_get_contents(dirname(__FILE__)."/../views/welcomemail/mail.txt");
		$file=preg_replace_callback('/\{\{\s*(\S+)\s*\}\}/',
			create_function('$matches',
				'
				$m=explode(".",$matches[1]); 
				$r=$GLOBALS["email"]; 
				foreach ($m as $l) {
					if (is_array($r) && array_key_exists($l,$r)) {
						$r=$r[$l]; 
					} else {
						$r="";
					}
				}
				return $r;
				'),
			$file);
		$file=str_replace("\r\n","\n",$file);
		list($headers,$body)=explode("\n\n",$file,2);
		$additional_headers="";
		foreach (explode("\n",$headers) AS $header) {
			list($key,$value)=explode(":",$header,2);
			if (strtoupper($key)=='TO') {
				$to=$value;
			} elseif (strtoupper($key)=='SUBJECT') {
				$subject=$value;
			} elseif ($additional_headers=="") {
				$additional_headers=$header;
			} else {
				$additional_headers.="\r\n$header";
			}
		}
		mail($to,$subject,$body,$additional_headers);
		print $to;
	}
	unset($_REQUEST['welcomemail']);
    }
}
