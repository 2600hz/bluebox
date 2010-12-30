<?php

require('getid3/getid3.php');

class MediaScanner {
  public static $default_rates = array(8000, 16000, 32000, 48000);

  public static function NormalizeFSNames($filename) {
    // NOTE: This is a FreeSWITCH-specific feature
    // Trim the 8000/16000/32000/48000 from directory names
    $replace = '/\/' . implode('\/|\/', self::$default_rates) . '\//';
    $filename = preg_replace($replace, '/', $filename);
    return $filename;
  }

    public static function filterKnownFiles($file) {
      $exists = file_exists($file['path']);

      if ( empty($file['registry']) || empty($file['registry']['rates']) ) {
	$file['registry']['rates'] = self::$default_rates;
      } else {
	$file['registry']['rates'] = array_merge($file['registry']['rates'], self::$default_rates);
      }

      foreach ( $file['registry']['rates'] as $rate ) {
	if ( ! $exists ) {
	  $base = basename($file['path']);
	  $filename = str_replace($base, $rate . '/' . $base, $file['path']);
	  $exists = file_exists($filename);
	}
      }

      if ( ! $exists ) {
	Doctrine_Query::create()
	  ->delete('MediaFile')
	  ->where('mediafile_id = ?', $file['mediafile_id'])
	  ->limit(1)
	  ->execute();
      }

      return $exists;
    }

    public static function scan($soundPath, $fileTypes) {
      set_time_limit(0);

      // TODO: Make this a queued event to scan all files. Only possible once.

      /*
       * Load everything into memory that we know about our existing sound files.
       * This may seem expensive but it shouldn't be - the info is tiny and a full
       * rescan will require all this data anyway.
       */

      // Get the list of known files already in the system
      $results = Doctrine_Query::create()
	->select('m.mediafile_id, m.file, m.registry')
	->from('MediaFile m')
	->execute(NULL, Doctrine::HYDRATE_ARRAY);

      $listedFiles = array();

      foreach ($results as $result) {
	$listedFiles[$result['file']] = array('mediafile_id' => $result['mediafile_id']
					      ,'registry' => $result['registry']
					      ,'path' => $soundPath . $result['file']
					      );
      }

      kohana::log('debug', 'Found ' . count($listedFiles) . ' listed files');

      if ( version_compare(PHP_VERSION, '5.2.3', '<') ) {
	$knownFiles = array();
	foreach ( $listedFiles as $idx => $file ) {
	  if ( self::filterKnownFiles($file) ) {
	    $knownFiles[$idx] = $file;
	  }
	}
	kohana::log('debug', 'foreach: Of ' . count($listedFiles) . ' listed, kept ' . count($knownFiles));
      } else {
	$knownFiles = array_filter($listedFiles, "MediaScanner::filterKnownFiles");
	kohana::log('debug', 'filter: Of ' . count($listedFiles) . ' listed, kept ' . count($knownFiles));
      }

      // TODO: Fix this. Download descriptions from the web?
      if (file_exists(MODPATH . 'mediamanager-1.0' . DIRECTORY_SEPARATOR . 'audio_descriptions.ini')) {
	$fp = fopen(MODPATH . 'mediamanager-1.0' . DIRECTORY_SEPARATOR . 'audio_descriptions.ini', 'r');
	while ($row = fgetcsv($fp)) {
	  $descriptions[$row[0]] = $row[1];
	}
      } else {
	$descriptions = array();
      }

      /*
       * Now compare what we know with what we find on disk and add any new stuff
       */
      // Initialize iterator
      $dir_iterator = new RecursiveDirectoryIterator($soundPath);
      $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

      // Read in a list of files already registered in the system
      $fileTypeMatch = '/^.+\.(' . implode('|', $fileTypes) . ')$/i';
      $regex = new RegexIterator($iterator, $fileTypeMatch, RecursiveRegexIterator::GET_MATCH);

      kohana::log('debug', 'Starting foreach for MediaScanner');
      $starttime = microtime(TRUE);

      foreach ($regex as $fileinfo) {
	$filename = $fileinfo[0];

	$shortname = str_replace($soundPath, '', self::NormalizeFSNames($filename));
	$framerate = basename(dirname($filename));

	// Is an existing one?
	if ( isset($knownFiles[$shortname]) ) {
	  $mediafile_id = $knownFiles[$shortname]['mediafile_id'];
	  $registry = (array)$knownFiles[$shortname]['registry'];

	  if ( ! in_array($framerate, (array)$registry['rates']) ) {
	    //$info = self::getAudioInfo($filename);
            $registry['rates'][] = $framerate;
	    //$registry = arr::merge((array)$registry, $info);

	    Doctrine_Query::create()
	      ->update('MediaFile m')
	      ->set('m.registry', '?', serialize($registry))
	      ->where('m.mediafile_id = ?', $mediafile_id)
	      ->execute();

              kohana::log('debug', 'Updating ' . $filename . ' with sample rate ' . $framerate . '...');

	    // Add to list of "known" files
	    $knownFiles[$shortname]['registry'] = $registry;
	  }
	} else {
	  kohana::log('debug', $filename . ' is a new file');

	  // NEW FILE! Do lots of stuff
	  $mediaFile = new MediaFile();
	  $mediaFile['file'] = $shortname;
	  $mediaFile['path'] = dirname($mediaFile['file']);   // We track the path separately to ease searching
	  $mediaFile['account_id'] = 1;

	  // See if we know this filename, description & category from the XML info
	  if (isset($descriptions[$shortname])) {
	    $mediaFile['description'] = $descriptions[$shortname];
	  } else {
	    $mediaFile['description'] = 'Unknown';
	  }

          try
          {
              $mediaFile['registry'] += self::getAudioInfo($filename);
              $mediaFile->save();
              // Add to list of "known" files
              $knownFiles[$mediaFile['file']] = array('mediafile_id' => $mediaFile['mediafile_id']
                                                      ,'registry' => $mediaFile['registry']
                                                      ,'path' => $soundPath . $mediaFile['file']
                                                      );
          }
          catch (Exception $e)
          {
             kohana::log('debug', 'Unable to save audio info: ' .$e->getMessage());
          }
	}
      }

      $endtime = microtime(TRUE);
      kohana::log('debug', 'scan foreach took ' . ($endtime - $starttime) . ' msec');
      Kohana::log('debug', 'Finished scanning sound files in ' . $soundPath);
    }

    /*public static function scanXml() {
        $xml = simplexml_load_file('/usr/local/src/freeswitch/docs/phrase/phrase_en.xml');
        $knownFiles = self::processXml($xml, '/usr/local/freeswitch/sounds/');
        // Need to write this out
        return $knownFiles;
        //echo serialize($knownFiles);
    }

    public static function processXml($xml, $curdirectory) {
        $knownFiles = array();
        foreach ($xml as $dirname => $element) {
            if ($element->getName() == 'prompt') {
                $attr = $element->attributes();
                $knownFiles['en/us/callie/' . $curdirectory . '/' . (string)$attr['filename']] = (string)$attr['phrase'];
            } else {
                $knownFiles += self::processXml($element, $dirname);
            }
        }

        return $knownFiles;
    }*/

    public static function getAudioInfo($filename) {
      $id3 = new getID3();
      $info = $id3->analyze($filename);

      if (!empty($info['error']))
      {
            throw new Exception(implode(' - ', $info['error']));
      }

      switch($info['audio']['dataformat']) {
      case 'wav' :
	return array('type' => $info['audio']['dataformat']
		     ,'compression' => $info['audio']['compression_ratio']
		     ,'channels' => $info['audio']['channels']
		     ,'rates' => array($info['audio']['streams'][0]['sample_rate'])
		     ,'byterate' => $info['audio']['bitrate']
		     ,'bits' => $info['audio']['bits_per_sample']
		     ,'size' => $info['filesize']
		     ,'length' => $info['playtime_seconds']
		     );
      case 'mp1' :
	return array('type' => $info['audio']['dataformat']
		     ,'compression' => $info['audio']['compression_ratio']
		     ,'channels' => $info['audio']['channels']
		     ,'rates' => array($info['audio']['streams'][0]['sample_rate'])
		     ,'byterate' => $info['audio']['bitrate']
		     ,'bits' => $info['audio']['bits_per_sample']
		     ,'size' => $info['filesize']
		     ,'length' => $info['playtime_seconds']
		     );
      case 'mp3' :
	return array('type' => $info['audio']['dataformat']
		     ,'compression' => $info['audio']['compression_ratio']
		     ,'channels' => $info['audio']['channels']
		     ,'rates' => array($info['audio']['sample_rate'])
		     ,'byterate' => $info['audio']['bitrate']
		     ,'bits' => NULL
		     ,'size' => $info['filesize']
		     ,'length' => $info['playtime_seconds']
		     );
      case 'ogg' :
	return array('type' => $info['audio']['dataformat']
		     ,'compression' => $info['audio']['compression_ratio']
		     ,'channels' => $info['audio']['channels']
		     ,'rates' => array($info['audio']['sample_rate'])
		     ,'byterate' => $info['audio']['bitrate']
		     ,'bits' => NULL
		     ,'size' => $info['filesize']
		     ,'length' => $info['playtime_seconds']
		     );
      default:
	kohana::log('error', 'Unhandled media type(' . $info['audio']['dataformat'] . ') for file ' . $filename);
	return array();
      }
    }
}
