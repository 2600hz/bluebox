<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_supervisor_Controller extends Bluebox_Controller
{
        static $agent_status_fields = array(
                'displayname' => 'Name',
                'name' => 'User',
                'status' => 'Status',
                'state' => 'State',
                'since_last_call_began' => 'Since Last Begin',
                'since_last_call_ended' => 'Since Last End',
                'since_last_call_offered' => 'Since Last Offered',
                'since_last_status_change' => 'Since Last Status'
        );

       static $agent_detail_fields = array(
                'Status' => array(
                        'last_bridge_start' => 'Last Call Start',
                        'last_bridge_end' => 'Last Call End',
                        'last_offered_call' => 'Last Offered',
                        'last_status_change' => 'Last Status Change'
                ),
                'Time' => array(
                        'talk_time' => 'On Calls',
                        'ready_time' => 'Waiting for Call'
                ),
                'Call Counts' => array(
                        'no_answer_count' => 'Not Answered',
                        'calls_answered' => 'Taken',
                ),
                'Configuration' => array(
                        'type' => 'Contact Strategy',
                        'contact' => 'Contact String',
                        'max_no_answer' => 'Auto Break No Answer Count',
                        'wrap_up_time' => 'Call Wrap up Time',
                        'reject_delay_time' => 'Delay on Reject',
                        'busy_delay_time' => 'Delay on Busy'
                )
        );

        static $agent_date_diffs = array(
                'since_last_call_began' => array('last_bridge_start' => 'now'),
                'since_last_call_ended' => array('last_bridge_end' => 'now'),
                'since_last_call_offered' => array('last_offered_call' => 'now'),
                'since_last_status_change' => array('last_status_change' => 'now')
        );

        static $agent_date_fields = array(
                'last_bridge_start' => 'last_bridge_start_formated',
                'last_bridge_end' => 'last_bridge_end_formated',
                'last_offered_call' => 'last_offered_call_formated',
                'last_status_change' => 'last_status_change_formated'
        );

        static $queue_status_fields = array(
                'caller_name' => 'Caller Name',
                'caller_number' => 'Caller Number',
                'serving_agent' => 'Agent',
                'state' => 'State',
                'time_in_system' => 'Total Time of Call',
                'time_in_queue' => 'Time in Queue',
                'time_with_agent' => 'Time with Agent',
                'time_rejoin' => 'Recycle Time',
                'time_since_abandon' => 'Abandon Time',
                'base_score' => 'Base Score',
                'skill_score' => 'Skill Score'
        );

        static $queue_date_fields = array(
                'system_epoch' => 'system_epoch_formated',
                'joined_epoch' => 'joined_epoch_formated',
                'rejoined_epoch' => 'rejoined_epoch_formated',
                'bridge_epoch' => 'bridge_epoch_formated',
                'abandoned_epoch' => 'abandoned_epoch_formated'
        );

        static $queue_date_diffs = array(
                'time_in_system' => array('system_epoch' => 'now'),
                'time_in_queue' =>  array('joined_epoch' => 'now'),
                'time_rejoin' => array('rejoined_epoch' => 'now'),
                'time_with_agent' => array('bridge_epoch' => 'now'),
                'time_since_abandon' => array('abandoned_epoch' => 'now')
        );

        private $queue_detail_fields = array(
                'Channel Info' => array(
                        'Unique-ID' => 'ID',
                        'Caller-Dialplan' => 'Dial Plan',
                        'Caller-Context' => 'Context',
                        'Caller-Source' => 'Source App'
                ),
                'Caller' => array(
                        'variable_remote_media_ip' => 'Net Addr',
                        'variable_remote_media_port' => 'Port'
                ),
                'Times' => array(
                        'Caller-Channel-Created-Time' => 'Channel Start',
                        'Caller-Channel-Answered-Time' => 'Answered',
                        'Caller-Channel-Hangup-Time' => 'Hangup',
                        'Caller-Channel-Transfer-Time' => 'Transfer'
                ),
                'Media' => array(
                        'variable_read_codec' => 'Read Codec',
                        'variable_read_rate' => 'Read Bitrate',
                        'variable_write_codec' => 'Write Codec',
                        'variable_write_rate' => 'Write Rate'
                )
        );

         private $queue_detail_datetimefields = array(
                'Event-Date-Timestamp',
                'Caller-Profile-Created-Time',
                'Caller-Channel-Created-Time',
                'Caller-Channel-Answered-Time',
                'Caller-Channel-Progress-Time',
                'Caller-Channel-Progress-Media-Time',
                'Caller-Channel-Hangup-Time',
                'Caller-Channel-Transfer-Time'
        );

        public function __construct()
        {
                parent::__construct();
                stylesheet::add('callmanager', 50);
                stylesheet::add('callcenter_supervisor', 50);
        }

        public function index()
        {
                $this->template->content = new View('generic/grid');
                $this->grid = jgrid::grid('callcenter_queue',
                        array(
                                'caption' => 'Queues'
                        )
                );
                $this->grid->add('ccq_id', 'ID',
                        array(
                                'hidden' => true,
                                'key' => true
                        )
                );
                $this->grid->add('ccq_name', 'Name');
                $this->grid->add('queueLocation/name', 'Location',
                        array(
                                'width' => '150',
                                'search' => false,
                        )
                );
                $this->grid->addAction('callcenter_supervisor/view', 'View',
                        array(
                                'arguments' => 'ccq_id'
                        )
                );
                plugins::views($this);
                $this->view->grid = $this->grid->produce();
        }

        public function view($queueId)
        {
                $this->template->content = new View('callcenter_supervisor/view');
                $queueObj = Doctrine::getTable('callcenter_queue')->find($queueId);
                $locationObj = Doctrine::getTable('Location')->find($queueObj->ccq_locationid);
                if ($queueObj)
                {
                        $this->view->queue_name_location = $queueObj->ccq_name . '@';
                        if ($locationObj)
                                $this->view->queue_name_location .= $locationObj->domain . ' (' . $locationObj->name . ')';
                        else
                                $this->view->queue_name_location .= 'Unknown (Unknown)';
                }
                else
                        $this->view->queueName = 'Unknown@Unknown (Unknown)';
                $this->view->queueid = $queueId;
                $this->view->queue_status_fields = self::$queue_status_fields;
                $this->view->agent_status_fields = self::$agent_status_fields;
        }

        public function getQueueList()
        {
                try {
                        $input = Input::instance();
                        $queueId = $input->post('queueid');
                        $queuelistview = new View('callcenter_supervisor/queuelist');
                        $queuelistview->status_fields = self::$queue_status_fields;
                        $queuelistview->showdetail = true;
                        $queuelistview->updated = date('r');

                        $callcenter_manager_obj = new CallCenterManager();
                        $queuelistview->queueStatus = $callcenter_manager_obj->getQueueStatus($queueId);

                        $filters = array();
                        $filters['state'] = $input->post('queuestate_filters');

                        foreach ($queuelistview->queueStatus as $uuid => $status_arr)
                        {
                                foreach (self::$queue_date_diffs as $fieldname => $fieldparts)
                                {
                                        $d1 = key($fieldparts) === 'now' ? 'now' : $status_arr[key($fieldparts)];
                                        $d2 = current($fieldparts) === 'now' ? 'now' : $status_arr[current($fieldparts)];
                                        $queuelistview->queueStatus[$uuid][$fieldname] = dttm::timestampdiff($d1, $d2);
                                }

                                foreach (self::$queue_date_fields as $fieldname => $formated_fieldname)
                                {
                                        $queuelistview->queueStatus[$uuid][$formated_fieldname] = date('r', $queuelistview->queueStatus[$uuid][$fieldname]);
                                }
                        }
                        arr::alfilter($queuelistview->queueStatus, $filters);
                        arr::alsort($queuelistview->queueStatus, $input->post('queue_order'));

                        $queuelistview->render(TRUE);
                } catch (Exception $e) {
                        echo 'An error has occured: ' . $e->getMessage() . '<br>';
                        if (strpos($e->getMessage(), 'Not connected'))
                                echo 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
                }
                exit();
        }

        public function getAgentList()
        {
                try {
                        $input = Input::instance();
                        $queueId = $input->post('queueid');
                        $agentlistview = new View('callcenter_supervisor/agentlist');
                        $agentlistview->status_fields = self::$agent_status_fields;
                        $agentlistview->showdetail = true;
                        $agentlistview->updated = date('r');

                        $callcenter_manager_obj = new CallCenterManager();
                        $agentlistview->agentStatus = $callcenter_manager_obj->getQueueAgentStatus($queueId);

                        $filters = array();
                        $filters['status'] = $input->post('agentstatus_filters');

                        foreach ($agentlistview->agentStatus as $agent => $agentdata)
                        {
                                $locationObj = Doctrine::getTable('Location')->findOneByDomain($agentdata['domain']);
                                $agentObj = Doctrine::getTable('callcenter_agent')->findOneBycca_loginidAndcca_locationid($agentdata['loginid'], $locationObj->location_id);
                                $agentlistview->agentStatus[$agent]['displayname'] = $agentObj->cca_displayname;
                                $agentlistview->agentStatus[$agent]['id'] = $agentObj->cca_id;

                                foreach (self::$agent_date_diffs as $fieldname => $fieldparts)
                                {
                                        $d1 = key($fieldparts) === 'now' ? 'now' : $agentdata[key($fieldparts)];
                                        $d2 = current($fieldparts) === 'now' ? 'now' : $agentdata[current($fieldparts)];
                                        $agentlistview->agentStatus[$agent][$fieldname] = dttm::timestampdiff($d1, $d2);
                                }

                                foreach (self::$agent_date_fields as $fieldname => $formated_fieldname)
                                {
                                        $agentlistview->agentStatus[$agent][$formated_fieldname] = date('r', $agentlistview->agentStatus[$agent][$fieldname]);
                                }
                        }
                        arr::alfilter($agentlistview->agentStatus, $filters);
                        arr::alsort($agentlistview->agentStatus, $input->post('agent_order'));

                        $agentlistview->render(TRUE);
                } catch (Exception $e) {
                        echo 'An error has occured: ' . $e->getMessage() . '<br>';
                        if (strpos($e->getMessage(), 'Not connected'))
                                echo 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
                }
                exit();
        }

        public function changeagentstatus($agentid = 0)
        {
                $input = Input::instance();
                if (is_array($input->post('submit')))
                {
                        if ($input->post('agentid') == '' || current($input->post('submit')) == 'cancel')
                        {
                                $this->exitQtipAjaxForm();
                                exit();
                        }
                        $this->template->content = new View('callcenter_supervisor/commandresponse');
                        try {
                                $callcenter_manager_obj = new CallCenterManager();
                                $commandresponse = $callcenter_manager_obj->setAgentStatus($input->post('agentid'), $input->post('status'));
                                if (trim($commandresponse) == '+OK')
                                        $this->view->commandresponse = 'The status was sucessfully changed.<script language="javascript">getAgentStatus();</script>';
                                else
                                        $this->view->commandresponse = 'An Error Has Occurred: <br>' . $commandrepsonse;
                        } catch (Exception $e) {
                                $this->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
                                if (strpos($e->getMessage(), 'Not connected'))
                                        $this->view->commandresponse = 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
                        }
                }
                else
                {
                        $agentObj = Doctrine::getTable('callcenter_agent')->find($agentid);
                        $this->template->content = new View('callcenter_supervisor/changestatus');
                        $this->view->agentid = $agentid;
                        $this->view->agentlogindomain = $agentObj->cca_loginid . '@' . $agentObj->agentLocation->domain;
                        $this->view->agentdisplayname = $agentObj->cca_displayname;
                }
        }

        public function getChannelDetail($uuid)
        {
                $callManagerObj = new callManager();
                $channelDetail_view = new View('callcenter_supervisor/channeldetail');
                $channelDetail_view->detailfields = $this->queue_detail_fields;
                try {
                        $channelDetail_view->channeldata = $callManagerObj->getChannelInfo($uuid, true);
                        if ($channelDetail_view->channeldata)
                                foreach ($this->queue_detail_datetimefields as $fieldname)
                                {
                                        if (isset($channelDetail_view->channeldata[$fieldname]) && !$channelDetail_view->channeldata[$fieldname] == 0)
                                                $channelDetail_view->channeldata[$fieldname] = date('r', $channelDetail_view->channeldata[$fieldname]);
                                        else
                                                $channelDetail_view->channeldata[$fieldname] = '';
                                }
                        $channelDetail_view->render(TRUE);
                } catch (Exception $e) {
                        echo 'An error has occured: ' . $e->getMessage() . '<br>';
                        if (strpos($e->getMessage(), 'Not connected'))
                                echo 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
                }
                exit();
        }
}