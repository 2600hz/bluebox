<?php

class Media {
    public static function files()
    {
        $q = Doctrine_Query::create()->select('f.mediafile_id, f.file, f.description')->from('MediaFile f');

        $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

        $files = array();
        
        foreach ($result as $file) {
            $files[$file['mediafile_id']] = $file['file'] . ' (' . substr($file['description'], 0, 30) . (strlen($file['description']) > 30 ? '...' : '') . ')';
        }
        
        return $files;
    }

    public static function getMediaFilename($filename, $sampleRate = NULL, $exists = FALSE) {
        $filename = Media::getMediaPath() . $filename;

        // If we're working with FreeSWITCH we need to tack on the samplerate to the folder name for accuracy if it's an 8/16/32/48k .WAV file
        if (Kohana::config('telephony.driver') == 'FreeSwitch') {
            $dirname = dirname($filename);
            $basename = basename($filename);

            // If there is a samplerate requested and it's a valid rate, try to use it
            if ($sampleRate and in_array($sampleRate, array('8000', '16000', '32000', '48000'))) {
                $newfile = $dirname . DIRECTORY_SEPARATOR . $sampleRate . DIRECTORY_SEPARATOR . $basename;
                if ($exists and file_exists($newfile)) {
                    return $newfile;
                } elseif ($exists) {
                    return $filename;
                } else {
                    return $filename;
                }
            } else {
                return $filename;
            }

        } else {
            // For Asterisk just return the file & dir as they were - no changes
            return $filename;
        }
    }

    /**
     * Get the base audio path where all sound files are located, for this particular driver
     */
    public static function getMediaPath() {
        $driver = strtolower(Kohana::config('telephony.driver'));
        return Kohana::config($driver . '.audio_root') . '/';
    }
}
