<?php defined('SYSPATH') or die('No direct access allowed.');

class SimpleRoute_Controller extends Bluebox_Controller
{
    protected $baseModel = 'SimpleRoute';

    public function  __construct()
    {
        jquery::addPlugin('uiCSS');

        javascript::add('mustache');

        parent::__construct();
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Simple Routes'
            )
        );

        // Add the base model columns to the grid
        $grid->add('simple_route_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        //$grid->add('pattern', 'Pattern');

        // Add the actions to the grid
        $grid->addAction('simpleroute/edit', 'Edit', array(
                'arguments' => 'simple_route_id'
            )
        );
        $grid->addAction('simpleroute/delete', 'Delete', array(
                'arguments' => 'simple_route_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        $types = array();

        switch(Telephony::getDriverName())
        {
            case 'FreeSwitch':
                $types = array(
                    SimpleRoute::TYPE_SIMPLE => 'Short Hand',
                    SimpleRoute::TYPE_REGEX => 'Regex'
                );
                break;

            default:
                $types = array(
                    SimpleRoute::TYPE_SIMPLE => 'Short Hand'
                );
        }

        $patternTemplate = new View('simpleroute/pattern.mus', array('mustache_template' => FALSE));

        $patternTemplate = json_encode((string)$patternTemplate);

        $patternTemplate = str_replace(array('\n', '  '), '', $patternTemplate);

        $this->view->patternTemplate = $patternTemplate;

        $this->view->types = $types;

        parent::prepareUpdateView($baseModel);
    }

    protected function pre_save(&$object)
    {
        $patterns = $object['patterns'];

        foreach($patterns as $key => $pattern)
        {
            if (empty($pattern))
            {
                unset($patterns[$key]);
            }
        }

        if (empty($patterns))
        {
            throw new Exception('You must provide at least one pattern');
        }

        $object['patterns'] = array_values($patterns);

        parent::pre_save($object);
    }

    protected function save_succeeded(&$object)
    {
        parent::save_succeeded($object);
        
        // One of those nasty but functionaly things...
        $trunks = Doctrine::getTable('Trunk')->findAll();

        foreach ($trunks as $trunk)
        {
            if (empty($trunk['plugins']['simpleroute']['patterns']))
            {
                continue;
            }

            if (!isset($trunk['plugins']['simpleroute']['patterns'][$object['simple_route_id']]))
            {
                continue;
            }

            kohana::log('debug', 'Rebuilding trunk ' .$trunk['trunk_id'] .' to apply the changes to simple route ' .$object['simple_route_id']);

            $trunk->markModified('name');

            $trunk->save();
        }
    }

    protected function delete_succeeded(&$object)
    {
        parent::delete_succeeded($object);
        
        $identifier = $object->identifier();

        if (empty($identifier['simple_route_id']))
        {
            return;
        }

        // One of those nasty but functionaly things...
        $trunks = Doctrine::getTable('Trunk')->findAll();

        foreach ($trunks as $trunk)
        {
            if (empty($trunk['plugins']['simpleroute']['patterns']))
            {
                continue;
            }

            if (!isset($trunk['plugins']['simpleroute']['patterns'][$identifier['simple_route_id']]))
            {
                $patterns = $trunk['plugins']['simpleroute']['patterns'];

                unset($patterns[$identifier['simple_route_id']]);

                $trunk['plugins']['simpleroute']['patterns'] = $patterns;
            }

            kohana::log('debug', 'Rebuilding trunk ' .$trunk['trunk_id'] .' to remove simple route ' .$identifier['simple_route_id']);

            $trunk->save();
        }
    }
}
