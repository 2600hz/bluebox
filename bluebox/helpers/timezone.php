<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/timezone
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class timezone
{    
    /**
    * Creates an HTML form select tag with all avaliable timezones
    * Mash-up of kohana dropdowns and code retrieved from:
    * http://usphp.com/manual/en/function.timezone-identifiers-list.php on 7/23/2009
    * Modified by K Anderson
    *
    * TODO: This needs to be refactored to use the kohana html helpers
    *
    * @param   string|array  input name or an array of HTML attributes
    * @param   string        option key that should be selected by default
    * @param   string        a string to be attached to the end of the attributes
    * @return  string
    */
    public static function dropdown($data, $selected = NULL, $extra = '')
    {
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        } 
        else
        {
            if (isset($data['options']))
            {
                // Use data options
                $options = $data['options'];
            }

            if (isset($data['selected']))
            {
                // Use data selected
                $selected = $data['selected'];
            }
        }

        $input = '<select'.form::attributes($data, 'select').' '.$extra.'>'."\n";

        if (empty($selected))
        {
            $selectedzone = date_default_timezone_get();
        }
        else
        {
            $selectedzone = $selected;
        }

        $input .= self::timezonechoice($selectedzone);
        
        $input .= '</select>';

        return $input;
    }

    public static function timezonechoice($selectedzone)
    {
        $all = timezone_identifiers_list();

        $i = 0;

        foreach($all AS $zone)
        {
            $zone = explode('/',$zone);

            $zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';

            $zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';

            $zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';

            $i++;
        }

        asort($zonen);

        $structure = '';

        foreach($zonen AS $zone)
        {
            extract($zone);

            if($continent == 'Africa' || $continent == 'America' || $continent == 'Antarctica' || $continent == 'Arctic' || $continent == 'Asia' || $continent == 'Atlantic' || $continent == 'Australia' || $continent == 'Europe' || $continent == 'Indian' || $continent == 'Pacific')
            {
                if(!isset($selectcontinent))
                {
                    $structure .= '<optgroup label="'.$continent.'">';
                }
                elseif($selectcontinent != $continent)
                {
                    $structure .= '</optgroup><optgroup label="'.$continent.'">';
                }

                if(isset($city) != '')
                {
                    if (!empty($subcity) != '')
                    {
                        $city = $city . '/'. $subcity;
                    }

                    $structure .= "<option ".((($continent.'/'.$city)==$selectedzone)?'selected="selected "':'')." value=\"".($continent.'/'.$city)."\">".str_replace('_',' ',$city)."</option>";
                }
                else
                {
                    if (!empty($subcity) != '')
                    {
                        $city = $city . '/'. $subcity;
                    }

                    $structure .= "<option ".(($continent==$selectedzone)?'selected="selected "':'')." value=\"".$continent."\">".$continent."</option>";
                }

                $selectcontinent = $continent;
            }
        }

        $structure .= '</optgroup>';

        return $structure;
    }
    
    public static function getUTCOffset($tz)
    {
        $timezone = new DateTimeZone($tz);

        $time = new DateTime('now', $timezone);

        $offset = $timezone->getOffset($time);

        return $offset;
    }
}