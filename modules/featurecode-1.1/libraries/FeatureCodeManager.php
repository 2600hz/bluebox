<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCodeManager
{
    public static function initializeFeatureCode()
    {
        self::echo_test();
    }

    public static function addFeatureCode($number, $xml)
    {
    }

    public static function removeFeatureCode($feature_code_id)
    {
        $featureCode = new FeatureCode();    
    }

    public static function provideNumberTargets()
    {
        $target_objects = array();

        $featurecodes = Doctrine::getTable('FeatureCode')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($featurecodes as $featurecode)
        {
            $target_objects[] = array(
                'object_name' => $featurecode['name'],
                'object_description' => 'Feature Code',
                'object_number_type' => 'FeatureCodeNumber',
                'object_id' =>  $featurecode['feature_code_id'],
            );
        }

        Event::$data['FeatureCodeNumber'] = array(
            'short_name' => 'featurecode',
            'display_name' => 'Feature Code',
            'target_objects' => $target_objects,
            'quick_add' => '/featurecode/create'
        );
    }

    public static function echo_test()
    {
        $featureCode = new FeatureCode();

        $featureCode['name'] = 'Echo Test';

        $featureCode['registry'] = array('feature' => 'echo');

        $featureCode->save();

        try
        {
            $location = Doctrine::getTable('Location')->findAll();

            if (!$location->count())
            {
                throw Exception('Could not find location id');
            }

            $location_id = arr::get($location, 0, 'location_id');

            $number = new Number();

            $number['user_id'] = users::getAttr('user_id');

            $number['number'] = '9999';

            $number['location_id'] = $location_id;

            $number['registry'] = array(
                'ignoreFWD' => '0',
                'ringtype' => 'ringing',
                'timeout' => 20
            );

            $dialplan = array(
                'terminate' => array(
                    'transfer' => 0,
                    'voicemail' => 0,
                    'action' => 'hangup'
                )
            );

            $number['dialplan'] = $dialplan;

            $number['class_type'] = 'FeatureCodeNumber';

            $number['foreign_id'] = $featureCode['feature_code_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $numberType = Doctrine::getTable('NumberType')->findOneByClass('FeatureCodeNumber');

            if (empty($numberType['number_type_id']))
            {
                return FALSE;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            $number->save();

            return $number['number_id'];
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());

            throw $e;
        }
    }
}
