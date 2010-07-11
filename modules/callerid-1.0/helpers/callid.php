<?php defined('SYSPATH') or die('No direct access allowed.');

class callid
{    
    public static function dropdown($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'Account Default'
        );

        $data = arr::update($data, 'class', ' callid_dropdown');

        // see if the module wants to allow null selections
        if (!empty($data['nullOption']))
        {
            $options = array('0' => $data['nullOption']);
        } 
        else
        {
            $options = array();
        }

        unset($data['nullOption']);

        // build an array of netlists sutable for the dropdown helper
        $numbers = Doctrine_Query::create()
            ->from('Number n')
            ->Where('LENGTH(n.number) >= 10 ')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        foreach ($numbers as $number)
        {
            $matches = array();

            preg_match('/^\+?1?([2-9][0-8][0-9])([2-9][0-9][0-9])([0-9]{4})$/', $number['number'], $matches);

            if (count($matches) == 4)
            {
                $options[$number['number_id']] = '( '.$matches[1] .' ) ' .$matches[2] .' - ' .$matches[3];
            } 
            else
            {
                $options[$number['number_id']] = $number['number'];   
            }

        }

        return form::dropdown($data, $options, $selected, $extra);
    }
}