<?php defined('SYSPATH') or die('No direct access allowed.');

class Regenerate_Controller extends Bluebox_Controller {

    public function index() {

        $loadedModels = Doctrine::getLoadedModels();

        foreach( $loadedModels as $model ) {

            $outputRows = Doctrine::getTable($model)->findAll();

            foreach( $outputRows as $outputRow ) {
                Telephony::set($outputRow, $outputRow->identifier());
            }
        }

        Telephony::save();
        Telephony::commit();

    }


}
