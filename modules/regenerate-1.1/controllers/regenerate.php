<?php defined('SYSPATH') or die('No direct access allowed.');

class Regenerate_Controller extends Bluebox_Controller {

    public function index() {

        $loadedModels = Doctrine::getLoadedModels();

        $driver = Telephony::getDriver();
     	    
        $driverName = get_class($driver);
            
        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }
       
        foreach( $loadedModels as $model ) {

            $modelDriverName = $driverName .'_' .$model .'_Driver';

            if ( ! class_exists($modelDriverName, TRUE)) {
                continue;
            }

            $outputRows = Doctrine::getTable($model)->findAll();

            foreach( $outputRows as $outputRow ) {
                Telephony::set($outputRow, $outputRow->identifier());
            }
        }

        Telephony::save();
        Telephony::commit();

    }


}
