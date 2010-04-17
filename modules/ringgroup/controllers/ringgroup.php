<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * ringgroup.php - Ring Group Controller
 *
 * @author K Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage RingGroup
 */
class RingGroup_Controller extends FreePbx_Controller
{
    public $writable = array(
        'name',
        'strategy',
        'location_id',
        'timeout',
        'fallback_number_id'
    ); // update this to match inputs from forms

    protected $baseModel = 'RingGroup';

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden device_id, class_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'My Ring Groups',
            'multiselect' => true
        ))->add('ring_group_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name')->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->add('memberCount', 'Members', array(
            'search' => false,
            'align' => 'center',
            'callback' => array(
                'function' => array($this, 'countMembers'),
                'arguments' => array('ring_group_id')
            )
        ))->addAction('ringgroup/edit', 'Edit', array(
            'arguments' => 'ring_group_id',
            'width' => '120'
        ))->addAction('ringgroup/delete', 'Delete', array(
            'arguments' => 'ring_group_id',
            'width' => '20'
        ))->navGrid(array('del' => true));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }
    public function countMembers($cell, $ring_group_id) {
        $row = Doctrine::getTable('RingGroupMember')->findByRingGroupId($ring_group_id);
        return count($row->toArray());
    }

    public function edit($id)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit Ring Group';
        $this->ringgroup = Doctrine::getTable('RingGroup')->find($id);
        
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->ringgroup) {
            // Send any errors back to the index
            $error = i18n('Unable to locate ring group id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // get the list of group members
            $ringGroupMembers = $this->input->post('_members', array());
            if (!empty($ringGroupMembers)) {
                // make each value a subarray with device_id and delay_ring keys
                array_walk($ringGroupMembers, create_function('&$v,$k', '$v = array("device_id" => $v, "delay_ring" => 0);'));
            }
            
            // sync the group members with the group
            $ringGroupMembers = array('RingGroupMember' => $ringGroupMembers);
            $this->ringgroup->synchronizeWithArray($ringGroupMembers);

            // check the fallback number and avoid constraint issue by setting it to NULL
            // instead of 0 if there is no fallback selected
            $fallbackNumber = $_POST['ringgroup']['fallback_number_id'];
            if (empty($fallbackNumber)) {
                $this->ringgroup->fallback_number_id = NULL;
                $_POST['ringgroup']['fallback_number_id'] = NULL;
            }

            if($this->ringgroup->RingGroupMember->isModified()) {
                // this forces the directory xml to be generated even if only
                // the members change
                $this->ringgroup->markModified('name');
            }

            //TelephonyListener::$changedModels[] = array('action' => 'update', 'record' => &$this->ringgroup);
            // save the ringgroup
            if ($this->formSave($this->ringgroup)) {
                url::redirect(Router_Core::$controller);
            }
        }
        
        // Allow our device object to be seen by the view
        $this->view->ringgroup = $this->ringgroup;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add Ring Group';
        $this->ringgroup = new RingGroup();
        $this->ringgroup['timeout'] = 30;
        
        if ($this->submitted()) {
            // get the list of group members
            $ringGroupMembers = $this->input->post('_members', array());
            if (!empty($ringGroupMembers)) {
                // make each value a subarray with device_id and delay_ring keys
                array_walk($ringGroupMembers, create_function('&$v,$k', '$v = array("device_id" => $v, "delay_ring" => 0);'));
            }

            // sync the group members with the group
            $ringGroupMembers = array('RingGroupMember' => $ringGroupMembers);
            $this->ringgroup->synchronizeWithArray($ringGroupMembers);

            // check the fallback number and avoid constraint issue by setting it to NULL
            // instead of 0 if there is no fallback selected
            $fallbackNumber = $_POST['ringgroup']['fallback_number_id'];
            if (empty($fallbackNumber)) {
                $_POST['ringgroup']['fallback_number_id'] = NULL;
            } 

            if ($this->formSave($this->ringgroup)) {
                url::redirect(Router_Core::$controller);
            }
        }
        $this->view->ringgroup = $this->ringgroup;
        plugins::views($this);
    }
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
