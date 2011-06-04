<?php defined('SYSPATH') or die('No direct access allowed.');

require_once('getid3/getid3.php');

class MediaLib
{
    public static function provideMediaWidget()
    {
        $view = new View('mediafile/widget');

        Media_Plugin::addComponent('Media File', $view);
    }

    public static function maintenance()
    {
        kohana::log('debug', 'Preforming maintenance on the media files');
        
	$accountId = users::getAttr('account_id'); 
	if(!empty($accountId))
	{ 
        	$records = Doctrine::getTable('MediaFile')->findAll();

	        foreach ($records as $record)
        	{
			kohana::log('debug', 'the path is '.$record->filepath(TRUE).' and does it exists? '.is_file($record->filepath(TRUE))  );			
       		     	//if (!file_exists($record->filepath(TRUE)))
			if(!is_file($record->filepath(TRUE)))
            		{
			     	//kohana::log('debug', 'mediafile_id to be deleted :'.$record->filepath(TRUE));
				kohana::log('debug', 'The mediafile_id ' .$record['mediafile_id'] .' no longer exists...');

	                	$record->delete();
        	    	}
        	}
	}
	else
		kohana::log('debug', 'Account_ID Empty or Invalid, canceling the maintenance on the media files');
    }

    public static function generateConfiguration()
    {
        list($media, $xml, $base) = Event::$data;

        if (!empty($media['mediafile']))
        {
            kohana::log('debug', 'Configuring an auto-attendant to use: ' .$media['mediafile']);

            $xml->setAttributeValue('', 'greet-long', $media['mediafile']);

            $xml->setAttributeValue('', 'greet-short', $media['mediafile']);
        }
    }

    public static function getAudioInfo($path)
    {
        if (!is_file($path))
        {
            kohana::log('debug', 'Asked for audio info on non-existant file: "' .$path .'"');

            return FALSE;
        }

        $id3 = new getID3();

        $info = $id3->analyze($path);

        if (!empty($info['error']))
        {
            kohana::log('debug', 'Unable to analyze "' .$path .'" because ' .implode(' - ', $info['error']));

            return FALSE;
        }

        switch($info['audio']['dataformat'])
        {
            case 'wav' :
                return array('type'         => $info['audio']['dataformat']
                             ,'compression' => number_format($info['audio']['compression_ratio'], 4)
                             ,'channels'    => $info['audio']['channels']
                             ,'rates'       => $info['audio']['streams'][0]['sample_rate']
                             ,'byterate'    => $info['audio']['bitrate']
                             ,'bits'        => $info['audio']['bits_per_sample']
                             ,'size'        => $info['filesize']
                             ,'length'      => number_format($info['playtime_seconds'], 4)
                             );

            case 'mp1' :
                return array('type'         => $info['audio']['dataformat']
                             ,'compression' => number_format($info['audio']['compression_ratio'], 4)
                             ,'channels'    => $info['audio']['channels']
                             ,'rates'       => $info['audio']['streams'][0]['sample_rate']
                             ,'byterate'    => $info['audio']['bitrate']
                             ,'bits'        => $info['audio']['bits_per_sample']
                             ,'size'        => $info['filesize']
                             ,'length'      => number_format($info['playtime_seconds'], 4)
                             );

            case 'mp3' :
                return array('type'         => $info['audio']['dataformat']
                             ,'compression' => number_format($info['audio']['compression_ratio'], 4)
                             ,'channels'    => $info['audio']['channels']
                             ,'rates'       => $info['audio']['sample_rate']
                             ,'byterate'    => $info['audio']['bitrate']
                             ,'bits'        => NULL
                             ,'size'        => $info['filesize']
                             ,'length'      => number_format($info['playtime_seconds'], 4)
                             );

            case 'ogg' :
                return array('type'         => $info['audio']['dataformat']
                             ,'compression' => number_format($info['audio']['compression_ratio'], 4)
                             ,'channels'    => $info['audio']['channels']
                             ,'rates'       => $info['audio']['sample_rate']
                             ,'byterate'    => $info['audio']['bitrate']
                             ,'bits'        => NULL
                             ,'size'        => $info['filesize']
                             ,'length'      => number_format($info['playtime_seconds'], 4)
                             );

            default:
                kohana::log('error', 'Unhandled media type(' . $info['audio']['dataformat'] . ') for file ' . $path);
        }

        return FALSE;
    }
}
