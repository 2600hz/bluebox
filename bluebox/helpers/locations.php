<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Locations
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class locations
{    
    public static function dropdown($data, $selected = NULL, $extra = '')
    {      
        // standardize the $data as an array, strings default to the class_type
        if ( !is_array($data) )
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $defaults = array(
            'nullOption' => FALSE,
            'multitenancy' => TRUE
        );

        $data = arr::merge($defaults, $data);
        
        $options = Location::dictionary($data['multitenancy']);

        if ($data['nullOption'])
        {
            array_unshift($options, $data['nullOption']);
        }

        $data = array_diff($data, $defaults);

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
    
    public function getLocationDomain($location_id)
    {
        $location = Doctrine::getTable('Location')->findOneByLocationId(array($location_id));

        if($location)
        {
            return $location['domain'];
        }
    }   
}
