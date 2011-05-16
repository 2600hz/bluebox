<?php defined('SYSPATH') or die('No direct access allowed.');

class ValetParkingManager
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $lots = Doctrine::getTable('ValetParkingLot')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($lots as $lot)
        {
            $target_objects[] = array(
                'object_name' => $lot['vpl_name'],
                'object_description' => 'Parking Lot',
                'object_number_type' => 'ValetParkingLotNumber',
                'object_id' =>  $lot['vpl_id'],
            );
        }

        Event::$data['ValetParkingLotNumber'] = array(
            'short_name' => 'parkinglot',
            'display_name' => 'Parking Lot',
            'target_objects' => $target_objects,
            'quick_add' => '/valetparking/create'
        );
    }
}