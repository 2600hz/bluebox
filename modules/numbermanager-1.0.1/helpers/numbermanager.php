<?php defined('SYSPATH') or die('No direct access allowed.');

class numbermanager
{
    public static function prepareNumberOptions($numberOptions)
    {
        $numberOptions += array(
            'timeout' => 30
        );
        
        if (!empty($numberOptions['ringtype']))
        {
            $numberOptions['ringtype_' .$numberOptions['ringtype']] = TRUE;
        }

        return $numberOptions;
    }

    public static function getContexts($base = NULL)
    {
        if (is_null($base))
        {
            $base = Event::$data;
        }

        $associatedContexts = array();

        $contexts['contexts'] =
            Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);

        try
        {
            $base->loadReference('NumberContext');

            foreach ($base['NumberContext'] as $numberContext)
            {
                $associatedContexts[] = $numberContext['context_id'];
            }

        } 
        catch (Exception $e)
        {}

        foreach($contexts['contexts'] as $key => $context)
        {
            if (in_array($context['context_id'], $associatedContexts)) 
            {
                $contexts['contexts'][$key]['associated'] = TRUE;
            }
        }

        return $contexts;
    }

    public static function getNumberTypes($base = NULL)
    {
        if (is_null($base))
        {
            $base = Event::$data;   
        }

        $associatedPools = array();

        $numberTypes['numberTypes'] =
            Doctrine::getTable('NumberType')->findAll(Doctrine::HYDRATE_ARRAY);

        try
        {
            $base->loadReference('NumberPool');

            foreach ($base['NumberPool'] as $pool)
            {
                $associatedPools[] = $pool['number_type_id'];   
            }

        } 
        catch (Exception $e)
        {}

        foreach($numberTypes['numberTypes'] as $key => $numberType)
        {
            if (in_array($numberType['number_type_id'], $associatedPools))
            {
                $numberTypes['numberTypes'][$key]['associated'] = TRUE;
            }

            $numberTypes['numberTypes'][$key]['display_name'] =
                str_replace('Number', '', $numberType['class']);
        }

        return $numberTypes;
    }

    public function formatNumber($number = '')
    {
        $matches = array();

        preg_match('/^\+?1?([2-9][0-8][0-9])([2-9][0-9][0-9])([0-9]{4})$/', $number, $matches);

        if (count($matches) == 4)
        {
            return '( '.$matches[1] .' ) ' .$matches[2] .' - ' .$matches[3];
        } 
        else
        {
            return $number;
        }
    }

    public function showRoute($number_id)
    {
        // TODO: We need to optimize this with a DQL query if possible
        $number = Doctrine::getTable('Number')->find($number_id);

        if (($number['class_type'] == '') and ($number['foreign_id'] == 0)) return '';

        try
        {
            $module = Doctrine::getTable(str_replace('Number', '', $number['class_type']))->find($number['foreign_id']);

            if (!$module)
            {
                return '';
            }

        }
        catch (Exception $e)
        {
            kohana::log('error', $e->getMessage());

            return '';
        }

        $base = substr($number['class_type'], 0, strlen($number['class_type']) - 6);

        switch ($base)
        {
            case 'Device':
                return html::anchor('devicemanager/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_device'));

                break;

            case 'Conference':
                return html::anchor('conference/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_conference'));

                break;

            case 'Voicemail':
                return html::anchor('voicemail/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_voicemail'));

                break;

            case 'AutoAttendant':
                return html::anchor('autoattendant/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_AutoAttendant'));

            case 'RingGroup':
                return html::anchor('ringgroup/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_RingGroup'));

                break;

            case 'TimeOfDay':
                return html::anchor('timeofday/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_TimeOfDay_Route'));

                break;
                
            case 'ExternalXfer':
                return html::anchor('externalxfer/edit/' .$number['foreign_id'], $module['name'] . ' (' . $base . ')', array('title' => 'Goto_this_ExternalXfer_Route'));

                break;
                
            default:
            	Kohana::log('debug', print_r($module, true));
                if (isset($module->name))
                {
                     return $module->name . ' (' . $base . ')';
                }
                else
                {
                    return '';
                }

        }
    }

    public function showContexts($number_id)
    {
        $contexts = Doctrine_Query::create()
            ->select('n.number_id, c.name')
            ->from('NumberContext n, n.Context c')
            ->where("n.number_id = ?", array($number_id))
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $assignedContexts = '';

        foreach ($contexts as $context)
        {
            $assignedContexts .= '<p>' .$context['Context']['name'] .'</p>';
        }

        if (empty($contexts))
        {
            return count($contexts);
        } 
        else
        {
            return "<a title='Assigned Contexts' tooltip='" .$assignedContexts ."' class='addInfo' href='#'>" .count($contexts) .'</a>';
        }
    }

    public function showPools($number_id)
    {
        $pools = Doctrine_Query::create()
            ->select('n.number_id, t.class')
            ->from('NumberPool n, n.NumberType t')
            ->where("n.number_id = ?", array($number_id))
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $assignedPools = '';

        foreach ($pools as $pool)
        {
            $assignedPools .= '<p>' .str_replace('Number', '', $pool['NumberType']['class']) .'</p>';
        }

        if (empty($pools))
        {
            return count($pools);
        } 
        else
        {
            return "<a title='Assigned to Pools' tooltip='" .$assignedPools ."' class='addInfo' href='#'>" .count($pools) .'</a>';
        }
    }    
}
