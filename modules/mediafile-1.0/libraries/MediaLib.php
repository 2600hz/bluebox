<?php defined('SYSPATH') or die('No direct access allowed.');

require_once('getid3/getid3.php');

class MediaLib
{
    public static function listMediaFiles()
    {
        $mediaFiles = Doctrine::getTable('MediaFile')->findAll();
        
        foreach ($mediaFiles as $mediaFile)
        {
            $name =  $mediaFile['name'] .' (' .$mediaFile['file'] .')';

            media::add('MediaFile', $mediaFile['mediafile_id'], $name);
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