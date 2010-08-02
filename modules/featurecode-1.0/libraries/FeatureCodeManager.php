<?php
class FeatureCodeManager
{
    public static function addFeatureCode($number, $xml) {

    }

    public static function removeFeatureCode($feature_code_id) {
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
}


