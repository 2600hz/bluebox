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

    public static function getFilePath($mediafile_id) {
        $file = Doctrine::getTable('MediaFile')->find($mediafile_id);
        return self::getAudioPath() . $file['file'];
    }

    /**
     * Get the base audio path where all sound files are located, for this particular driver
     */
    public static function getAudioPath() {
        $driver = strtolower(Kohana::config('telephony.driver'));
        return Kohana::config($driver . '.audio_root') . '/';
    }
}
