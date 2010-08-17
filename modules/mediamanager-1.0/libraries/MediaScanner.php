<?php

require('getid3/getid3.php');

class MediaScanner {
    public static function NormalizeFSNames($filename) {
        // NOTE: This is a FreeSWITCH-specific feature
        // Trim the 8000/16000/32000/48000 from directory names
        $filename = preg_replace('/\/8000\/|\/16000\/|\/32000\/|\/48000\//', '/', $filename);
        return $filename;
    }

    public static function filterKnownFiles($file) {
      $exists = FALSE;

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

      $knownFiles = array_filter($listedFiles, "self::filterKnownFiles");

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

	// Is this a new file or an existing one?
	if ( isset($knownFiles[$shortname]) ) {
	  $mediafile_id = $knownFiles[$shortname]['mediafile_id'];
	  $registry = $knownFiles[$shortname]['registry'];

	  if ( ! in_array($framerate, $registry['rates']) ) {
	    $registry = array_merge($registry, self::getAudioInfo($filename));

	    Kohana::log('debug', 'Updating ' . $shortname . ' with sample rate ' . $framerate . '... ');

	    Doctrine_Query::create()
	      ->update('MediaFile')
	      ->set('registry', '?', serialize($registry))
	      ->where('mediafile_id = ?')
	      ->execute($mediafile_id);
	    unset($audioFile);
	  } else {
	    //Kohana::log('debug', 'SKIPPED - Nothing to update on ' . $shortname);
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

	  Kohana::log('debug', 'Adding ' . $mediaFile['file'] . " to the database.");

	  $mediaFile['registry'] += self::getAudioInfo($filename);

	  $mediaFile->save();

	  // Add to list of "known" files
	  $knownFiles[$mediaFile['file']] = $mediaFile['mediafile_id'];
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

      switch($info['audio']['dataformat']) {
      case 'wav' :
	return array('type' => $info['audio']['dataformat']
		     ,'compression' => $info['audio']['compression_ratio']
		     ,'channels' => $info['audio']['channels']
		     ,'rates' => array($info['audio']['sample_rate'])
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
