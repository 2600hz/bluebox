<?php defined('SYSPATH') or die('No direct access allowed.');

class Calls_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Calls';


    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Calls'
            )
        )
        // Add the base model columns to the grid
        ->add('calls_id', 'ID', array(
                'hidden' => true,
                'key' => true
        ))
        ->add('start_stamp', 'Start', array(
                'callback' => array(
                    'arguments' => 'calls_id',  
                    'function' => array($this, '_showCall')
                )
        ))
        ->add('caller_id_number', 'Calling Party')
        ->add('destination_number', 'Called Party')
        ->add('duration', 'Length')
        ->add('hangup_cause', 'Length')
        // Add the actions to the grid
        ->addAction('calls/edit', 'Edit', array(
                'arguments' => 'calls_id',
                'width' => '120'
            )
        )
        ->addAction('calls/delete', 'Delete', array(
                'arguments' => 'calls_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function download() {
        // Download a CDR

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Calls'
            )
        )
        // Add the base model columns to the grid
        ->add('calls_id', 'ID', array(
                'hidden' => true,
                'key' => true
        ))
        ->add('name', 'Name')
        // Add the actions to the grid
        ->addAction('calls/edit', 'Edit', array(
                'arguments' => 'calls_id',
                'width' => '120'
            )
        )
        ->addAction('calls/delete', 'Delete', array(
                'arguments' => 'calls_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function downloadAsCsv($startDate, $endDate, $otherStuff) {
        // Actual download processing (after Csv is selected)
    }

    public function downloadAsXml($startDate, $endDate, $otherStuff) {
        // Actual download processing (after Xml is selected)
    }

    public function import() {
        // Manually import a CDR
        ProcessLog::importLogs();
    }

    public function _showCall($NULL, $calls_id)
    {

        $coreFields = array(
            'uuid' => 'Unique ID',
            'accountcode' => 'Account Code',
            'caller_id_number' => 'Caller ID Number',
            'destination_number' => 'Desitnation',
            'context' => 'Context',
            'duration' => 'Duration Seconds',
            'start_stamp' => 'Start',
            'answer_stamp' => 'Answered',
            'end_stamp' => 'End',
            'billsec' => 'Billable Seconds',
            'hangup_cause' => 'Call End Cause',
            'channel_name' => 'Src Channel',
            'bridge_channel' => 'Dest Chanel'
        );



        $call = Doctrine::getTable('Calls')->find($calls_id)->toArray();

        $callDetail = '<table>';

        foreach ($call as $field => $value)
        {
            if(isset($coreFields[$field])) {
                $callDetail .= '<tr><th>' . $coreFields[$field] . '</th><td>' . $value .'</td></tr>';
            }
        }

        foreach ($call['registry'] as $field => $value)
        {
            $callDetail .= '<tr><th>' . $field . '</th><td>' . $value .'</td></tr>';
        }

        $callDetail .= '</table>';

        return "<a title='Call Info' tooltip='" . $callDetail ."' class='addInfo' href='#'>" . $call['start_stamp'] .'</a>';
    }

}
