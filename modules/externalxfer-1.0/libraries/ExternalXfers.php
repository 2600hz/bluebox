<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalXfers
{
    public static function provideNumberOptions()
    {
        Event::$data['ExternalXferNumber'] = 'externalxfer/numberOptions.mus';
    }

    public static function provideNumberTargets()
    {
        $target_objects = array();

        $externalXfers = Doctrine::getTable('ExternalXfer')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($externalXfers as $externalXfer)
        {
            $target_objects[] = array(
                'object_name' => $externalXfer['name'],
                'object_description' => 'External Transfer',
                'object_number_type' => 'ExternalXferNumber',
                'object_id' =>  $externalXfer['external_xfer_id'],
            );
        }

        Event::$data['ExternalXferNumber'] = array(
            'short_name' => 'externalxfer',
            'display_name' => 'External Transfer',
            'target_objects' => $target_objects,
            'quick_add' => '/externalxfer/create'
        );
    }
}