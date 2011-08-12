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

    public static function getAvailableRingGroupMembers()
    {
        $members = &Event::$data;

        $externalXfers = Doctrine::getTable('ExternalXfer')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($externalXfers as $externalXfer)
        {
            if (!empty($externalXfer['route_details'])) {
		    switch($externalXfer['route_type'])
		    {
			    case ExternalXfer::TYPE_SIP:

				    if (empty($externalXfer['route_details']['interface']) OR empty($externalXfer['route_details']['sipuri']))
				    {

				    }

				    $bridge = 'sofia/sipinterface_' .$externalXfer['route_details']['interface'] .'/' .$externalXfer['route_details']['sipuri'];

				    break;

			    case ExternalXfer::TYPE_TRUNK:

				    if (empty($externalXfer['route_details']['trunk']) OR empty($externalXfer['route_details']['number']))
				    {

				    }

				    $bridge = 'sofia/gateway/trunk_' .$externalXfer['route_details']['trunk'] .'/' .$externalXfer['route_details']['number'];

				    break;
		    }

		    $member = array(
				    'bridge' => $bridge,
				    'display_name' => $externalXfer['name'],
				    'display_type' => 'External Destination',
				    'id' => $externalXfer['external_xfer_id'],
				    'type' => 'ExternalXfer'
				   );
            }
            else
            {
                continue;
            }

            $members[] = $member;
        }
    }
}