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
        if ( !is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // append or insert the class
        $data = arr::update($data, 'class', ' locations_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption']))
        {
            $options = array(0 => $data['nullOption']);
        } 
        else
        {
            $options = array();
        }
        
        unset($data['nullOption']);

        // list all the locations from the location table
        $locations = Doctrine::getTable('Location')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($locations as $location)
        {
            $options[$location['location_id']] = $location['name'];// . ' (' . $location['domain'] .')';
        }

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
    
    public function getLocationDomain($location_id)
    {
        $location = Doctrine::getTable('Location')->findOneByLocationId(array($location_id));

        if($location)
        {
            return $location->domain;
        }
    }   
}
