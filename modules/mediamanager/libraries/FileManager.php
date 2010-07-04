<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Add all functions that relate to manipuling files on the fs here.
 * 
 * 
 */
class FileManager
{
        public static function dropdown ($data, $selected = NULL, $extra = '', $filter = NULL) {

            $files = self::ls($filter);
            $options = array(0 => __('Select'));
            foreach ($files as $file) {
                $options[$file['file_id']] = $file['name'];
            }

            // add in a class for skins
            if ( ! is_array($data))
            {
                $data = array('name' => $data);
            }
            $data = arr::update($data, 'class', ' file_dropdown');

            return form::dropdown($data, $options, $selected, $extra);
        }


	public static function getFilePath($file_id)
	{
		$file = Doctrine::getTable('File')->find($file_id);
		$user_id = $file->user_id;
		return sprintf(Kohana::config('upload.directory') . "/%s/%s", $user_id, $file->name);
	}
        
	/**
	 * 
	 * @todo Add more supported filters
	 * 
	 */
	public static function ls($filter = null)
	{
		// inputs: audio, images
		
		$q = Doctrine_Query::create()->select('f.file_id, f.name')->from('File f')->where(sprintf('f.user_id = %d', $_SESSION['user_id']));

		if(!is_null($filter))
		{
			if(in_array('audio', $filter))
			{
				$q = $q->andWhere("f.type LIKE 'audio%'");
			}
			
			if(in_array('image', $filter))
			{
				$q = $q->andWhere("f.type LIKE 'image%'");
			}
		}


		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;
	}
		
	public static function addDirectory($path, $user_id = 1)
	{
		static $level = 0;
		$dh =  new DirectoryIterator($path);
		foreach($dh as $dir)
		{
			if(!$dh->isDot())
			{
				self::addFile($dh->getPath(), $dh->getFilename(), $user_id);
			}	
		}
	}
	
	/**
	 * Add an existing file on teh system or update it.
	 * $path must be absolute
	 * @TODO add replace function
	 */
	public static function addFile($path, $name, $user_id = 1, $replace = false)	
	{
		$fullPath = $path. '/' . $name;
                /*
                 * We can use ffmpeg to extract some useful information about codec, bitrate or sample freq.
                 */
		if(extension_loaded('ffmpeg'))
		{
			$media = new ffmpeg_movie($fullPath);
			$duration = $media->hasAudio() ? sprintf("%01.2f", $media->getDuration()) : $duration;
			$audio_bit_rate = $media->hasAudio() ? $media->getAudioBitRate() : $audio_sample_rate;
			$audio_sample_rate = $media->hasAudio() ? $media->getAudioSampleRate() : $audio_sample_rate;
			$audio_codec = $media->hasAudio() ? $media->getAudioCodec() : $audio_codec;
		}
		//var_dump($audio_sample_rate);
		if($replace)
		{
			//var_dump($path, $name);
			$f = Doctrine::getTable('File')->findOneByPathAndName($path, $name);
		} else {
			$f = new File();
		}
		
		if(!$f)
		{
			$f = new File();
		}
		
		$f->user_id = $user_id;
		$f->name = $name;
		$f->size = filesize($fullPath);
		$f->type = mime_content_type($fullPath);
		$f->description = $name;
		$f->path = $path . '/';
		$f->duration = $duration;
		$f->audio_bit_rate = $audio_bit_rate;
		$f->audio_sample_rate = $audio_sample_rate;
		try {
			$f->save();
		} catch(Exception $e)
		{
			//echo "asdf";
		}
		
	}
	
	public function scanSystemSounds($basePath = '/opt/freeswitch/sounds/')
	{
		$files = self::getDirectory($basePath);
		$i = 0;
		foreach($files as $file)
		{
			$name = basename($file);
			$path =  dirname($file);
			self::addFile($path, $name, 1, TRUE);
			$i++;
			if($i > 200)
			{
				return; 
			}
		}
	}
	
	private static  function getDirectory( $path, $level = 0, &$files = array() ){ 
	
	    
	    $ignore = array('.', '..' ); 
	    // Directories to ignore when listing output. Many hosts 
	    // will deny PHP access to the cgi-bin. 
	
	    $dh = @opendir( $path ); 
	    // Open the directory to the handle $dh 
	     
	    while( false !== ( $file = readdir( $dh ) ) ){ 
	    // Loop through the directory 
	     
	        if( !in_array( $file, $ignore ) ){ 
	        // Check that this file is not to be ignored 
	             
	            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
	            // Just to add spacing to the list, to better 
	            // show the directory tree. 
	             
	            if( is_dir( "$path/$file" ) ){ 
	            // Its a directory, so we need to keep reading down... 
	             
	                //echo "<strong>$spaces $file</strong><br />"; 
	                self::getDirectory( "$path/$file", ($level+1), $files ); 
	                // Re-call this same function but on a new directory. 
	                // this is what makes function recursive. 
	             
	            } else { 
	             
	                $files[] =  "$path/$file"; 
	                // Just print out the filename 
	             
	            } 
	         
	        } 
	     
	    } 
	     
	    closedir( $dh ); 
	    
	    return $files;
	    // Close the directory handle 
	
	}
}
