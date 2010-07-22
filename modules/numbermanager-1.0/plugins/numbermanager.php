<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberManager_Plugin extends Bluebox_Plugin
{
    public function terminateOptions($return = FALSE)
    {
        $subview = new View('numbermanager/terminateOptions.mus');

        $subview->tab = 'main';

        $subview->section = 'terminate_options';

        $subview->render_conditional = array(
            'qtipAjaxForm' => FALSE,
            'ajax' => FALSE
        );

        $terminators = array();

        $numberTerminatorTemplates = array();

        $event_data = Event::$data;

        Event::run('numbermanager.collectNumberTerminators', $terminators);

        Event::$data = $event_data;

        $terminateOptions = array();

        if (isset($this->number['dialplan']['terminate']))
        {
            $terminateOptions = $this->number['dialplan']['terminate'];
        }

        if (empty($terminateOptions['action']))
        {
           $terminateOptions['action'] = 'hangup';
        }

        $subview->terminate = $terminateOptions;

        $subview->terminate_action = $terminateOptions['action'];

        $subview->terminators = $terminators;

        if ($return)
        {
            return $subview;
        }
        
        $this->views[] = $subview;
    }

    public function numberContexts()
    {
        $subview = new View('numbermanager/numberContexts');

        $subview->tab = 'main';

        $subview->section = 'number_contexts';

        $subview->contexts = numbermanager::getContexts($this->number);

        $this->views[] = $subview;
    }

    public function numberPools()
    {
        if (!empty($this->view->create_number_type))
        {
            return;
        }

        $subview = new View('numbermanager/numberPools');

        $subview->tab = 'main';

        $subview->section = 'number_pools';

        $subview->numberTypes = numbermanager::getNumberTypes($this->number);

        $this->views[] = $subview;
    }

    public function numberTargets()
    {
        if (!empty($this->view->create_number_type))
        {
            return;
        }

        $subview = new View('numbermanager/numberTargets');

        $subview->tab = 'main';

        $subview->section = 'number_targets';

        $targets = array();

        $numberOptionTemplates = array();

        $event_data = Event::$data;

        Event::run('numbermanager.collectNumberTargets', $targets);

        Event::run('numbermanager.collectNumberOptions', $numberOptionTemplates);
        
        Event::$data = $event_data;

        $numberTargets['targets'][] = array(
            'short_name' => 'system',
            'display_name' => 'System',
            'target_objects' => array(
                array(
                    'object_name' => 'Hangup',
                    'object_description' => 'System Action',
                    'object_number_type' => 'SystemNumber',
                    'object_id' => '0'
                )
            )
        );

        $subview->number = $this->number;

        $subview->targets = $targets;

        $subview->numberOptions = $numberOptionTemplates;

        $subview->registry = numbermanager::prepareNumberOptions($this->number['registry']);

        $this->views[] = $subview;
    }

    public function numberInventory()
    {
        $subview = new View('numbermanager/numberInventory');

        $subview->tab = 'main';

        $subview->section = 'number_inventory';

        $subview->render_conditional = array(
            'qtipAjaxForm' => FALSE,
            'ajax' => FALSE
        );

        $base = $this->getBaseModelObject();

        if (!$base)
        {
            return TRUE;
        }

        if (get_parent_class($base) == 'Bluebox_Record')
        {
            $class_type = get_class($base) .'Number';
        } 
        else
        {
            $class_type = get_parent_class($base) .'Number';
        }

        $identifiers = $base->identifier();

        if (!empty($identifiers))
        {
            $foreign_id = reset($identifiers);
        } 
        else
        {
            $foreign_id = 0;
        }

        $numbers = array(
            'avaliable' => array(),
            'assigned' => array()
        );

        $numberOptionTemplates = array();

        $event_data = Event::$data;
        
        Event::run('numbermanager.collectNumberOptions', $numberOptionTemplates);

        Event::$data = $event_data;

        $numbers['assigned'] = Doctrine_Query::create()
            ->from('Number n')
            ->where('foreign_id = ?', array($foreign_id))
            ->andWhere('class_type = ?', $class_type)
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $numbers['avaliable'] = Doctrine_Query::create()
            ->select('np.number_type_id, nt.class, n.*')
            ->from('NumberPool np, np.Number n, np.NumberType nt')
            ->whereIn('n.foreign_id', array(0, 'NULL'))
            ->andwhereIn('nt.class', array($class_type))
            ->orderBy('number')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        foreach ($numbers['assigned'] as $key => $number)
        {
            $numbers['assigned'][$key]['registry'] =
                numbermanager::prepareNumberOptions($number['registry']);
        }

        $subview->assignedNumberTemplate = self::getAssignedNumberTemplate($numberOptionTemplates, $class_type);

        $subview->avaliableNumberTemplate = self::getAvaliableNumberTemplate();

        $subview->numberOptionTemplates = $numberOptionTemplates;

        $subview->numbers = $numbers;

        $subview->class_type = $class_type;

        $this->views[] = $subview;

        return TRUE;
    }

    protected static function getAvaliableNumberTemplate()
    {
        $avaliableNumberTemplate = new View('numbermanager/avaliableNumber.mus', array('mustache_template' => FALSE));

        $avaliableNumberTemplate = json_encode((string)$avaliableNumberTemplate);

        $avaliableNumberTemplate = str_replace(array('\n', '  '), '', $avaliableNumberTemplate);

        return $avaliableNumberTemplate;        
    }

    protected static function getAssignedNumberTemplate($numberOptionTemplates, $class_type)
    {
        $assignedNumberTemplate = new View('numbermanager/assignedNumber.mus', array('mustache_template' => FALSE));

        $assignedNumberTemplate->mustache_template = FALSE;
        
        if (!empty($numberOptionTemplates[$class_type]))
        {
            $assignedNumberTemplate->numberOptionTemplate = $numberOptionTemplates[$class_type];            
        }

        $assignedNumberTemplate = json_encode((string)$assignedNumberTemplate);

        $assignedNumberTemplate = str_replace(array('\n', '  '), '', $assignedNumberTemplate);

        return $assignedNumberTemplate;
    }
}