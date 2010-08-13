<?php

class MediaScanner {
    public static function NormalizeFSNames($filename) {
        // NOTE: This is a FreeSWITCH-specific feature
        // Trim the 8000/16000/32000/48000 from directory names
        $filename = preg_replace('/\/8000\/|\/16000\/|\/32000\/|\/48000\//', '/', $filename);
        return $filename;
    }

    public static function scan($soundPath, $fileTypes)
    {
        set_time_limit(0);
        // TODO: Make this a queued event to scan all files. Only possible once.

        /*
         * Load everything into memory that we know about our existing sound files.
         * This may seem expensive but it shouldn't be - the info is tiny and a full
         * rescan will require all this data anyway.
         */

        // Get the list of known files already in the system
        $results = Doctrine::getTable('MediaFile')->findAll(Doctrine::HYDRATE_ARRAY);
        $knownFiles = array();
        foreach ($results as $result) {
            $knownFiles[$result['mediafile_id']] = $result['file'];
        }

        // TODO: Fix this. Download descriptions from the web?
        if (file_exists(MODPATH . 'mediamanager-1.0/audio_descriptions.ini')) {
            $fp = fopen(MODPATH . 'mediamanager-1.0/audio_descriptions.ini', 'r');
            while ($row = fgetcsv($fp)) {
                $descriptions[$row[0]] = $row[1];
            }
        } else {
            $descriptions = array();
        }

        /*
         * Now compare what we know with what we find on disk and add any new stuff
         */

        // Initialize audio analysis routine
        $audioFile = new AudioFile();

        // Initialize iterator
        $dir_iterator = new RecursiveDirectoryIterator($soundPath);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        // Read in a list of files already registered in the system
        foreach ($iterator as $filename) if (preg_match('/^.+\.(' . implode('|', $fileTypes) . ')$/i', $filename) and ($filename->isFile())) {
            $audioFile->loadFile($filename);
            $shortname = str_replace($soundPath, '', self::NormalizeFSNames($filename));

            // Is this a new file or an existing one?
            if ($mediafile_id = array_search($shortname, $knownFiles)) {
                // Yes, existing file! Just make sure the rate is in here. Good enough for now.
                $mediaFile = Doctrine::getTable('MediaFile')->find($mediafile_id);

                // Note that this is a bit dangerous and could use improvement.
                // We assume that all other properties in the file we just found match the file already uploaded.
                // That means if someone uploads the wrong audio file, it kinda messes things up big time.
                if (!in_array($audioFile->wave_framerate, (array)$mediaFile['registry']['rates'])) {
                    Kohana::log('debug', 'Updating ' . $shortname . " with sample rate " . $audioFile->wave_framerate . "... ");
                    $mediaFile['registry'] = array_merge_recursive($mediaFile['registry'], array('rates' => $audioFile->wave_framerate));;
                    $mediaFile->save();
                } else {
                    Kohana::log('debug', 'SKIPPED - Nothing to update on ' . $shortname . " with sample rate " . $audioFile->wave_framerate . "... ");
                }

            } else {
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

                $audioInfo = array( 'type' => $audioFile->wave_type,
                                    'compression' => $audioFile->wave_compression,
                                    'channels' => $audioFile->wave_channels,
                                    'rates' => $audioFile->wave_framerate,
                                    'byterate' => $audioFile->wave_byterate,
                                    'bits' => $audioFile->wave_bits,
                                    'size' => $audioFile->wave_size,
                                    'length' => $audioFile->wave_length);

                $mediaFile['registry'] += $audioInfo;

                $mediaFile->save();

                // Add to list of "known" files
                $knownFiles[$mediaFile['mediafile_id']] = $mediaFile['file'];
            }
        }

        Kohana::log('debug', 'Finished scanning sound files in ' . $soundPath);
        flush();exit();
    }

    public static function scanXml() {
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
    }
}
