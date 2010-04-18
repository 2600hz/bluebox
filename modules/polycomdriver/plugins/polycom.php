<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * freeswitch.php - Provides logic for the installer telephony configuration step
 * @author K Anderson
 * @license MPL
 * @package FreePBX3
 */
class Polycom_Plugin extends FreePbx_Plugin
{
    public static $phoneParameterMappings = array(
        'User Preferences' => array(
            'useDirectoryNames' => array('label' => 'Use Directory Names', 'type' => 'checkbox'),
            'oneTouchVoiceMail' => array('label' => 'One Touch Voicemail', 'type' => 'checkbox'),
            'headsetMode' => array('label' => 'Headset Mode', 'type' => 'checkbox'),
            'handsfreeMode' => array('label' => 'CID Number First', 'type' => 'checkbox'),
            'numberFirstCID' => array('label' => 'Hands Free Mode', 'type' => 'checkbox'),
        ),
        'Features' => array(
            'feature_1_enabled' => array('label' => 'Presence', 'type' => 'checkbox'),
            'feature_2_enabled' => array('label' => 'Messaging', 'type' => 'checkbox'),
            'feature_3_enabled' => array('label' => 'Directory', 'type' => 'checkbox'),
            'feature_4_enabled' => array('label' => 'Call List', 'type' => 'checkbox'),
            'feature_5_enabled' => array('label' => 'Ring Download', 'type' => 'checkbox'),
            'feature_6_enabled' => array('label' => 'Call List Received', 'type' => 'checkbox'),
            'feature_7_enabled' => array('label' => 'Call List Placed', 'type' => 'checkbox'),
            'feature_8_enabled' => array('label' => 'Call List Missed', 'type' => 'checkbox'),
            'feature_9_enabled' => array('label' => 'URL Dialing', 'type' => 'checkbox'),
            'feature_10_enabled' => array('label' => 'Call Park', 'type' => 'checkbox'),
            'feature_11_enabled' => array('label' => 'Group Call Pickup', 'type' => 'checkbox'),
            'feature_12_enabled' => array('label' => 'Directed Call Pickup', 'type' => 'checkbox'),
            'feature_13_enabled' => array('label' => 'Last Call Return', 'type' => 'checkbox'),
            'feature_14_enabled' => array('label' => 'ACD Login/Logout', 'type' => 'checkbox', 'class' => 'determinant agent_for_acdLoginOut'),
            'feature_15_enabled' => array('label' => 'ACD Agent Availability', 'type' => 'checkbox', 'class' => 'determinant agent_for_avalAgent')
        ),
        'Display' => array(
            'localClockEnabled' => array('label' => '24 Hour Clock', 'type' => 'checkbox'),
            '24HourClock' => array('label' => 'One Touch Voicemail', 'type' => 'checkbox'),
            'datetime_longFormat' => array('label' => 'Datetime Long Format', 'type' => 'checkbox'),
            'datetime_dateTop' => array('label' => 'Datetime on Top', 'type' => 'checkbox'),
            'datetime_format' => array('label' => 'Datetime Format:', 'type' => 'input'),
        ),
        'SNTP' => array(
            'sntp_overrideDHCP' => array('label' => 'Override DHCP SNTP Address', 'type' => 'checkbox', 'class' => 'determinant agent_for_overrideDHCP'),
            'sntp_address' => array('label' => 'SNTP Address:', 'type' => 'input', 'class' => 'dependent_positive rely_on_overrideDHCP'),
            'sntp_overrideOffset' => array('label' => 'Override DHCP SNTP Offset', 'type' => 'checkbox', 'class' => 'determinant agent_for_sntpOffset'), //FIX ME!
            'sntp_gmtOffset' => array('label' => 'SNTP Offset:', 'type' => 'input', 'class' => 'dependent_positive rely_on_sntpOffset'),
            'daylightSavings_enable' => array('label' => 'Daylight Savings Enabled', 'type' => 'checkbox'),
        ),
        'Hold' => array(
            'hold_reminder_enabled' => array('label' => 'Hold Reminder', 'type' => 'checkbox', 'class' => 'determinant agent_for_holdRemind'),
            'hold_reminder_period' => array('label' => 'Reminder Period:', 'type' => 'input', 'class' => 'dependent_positive rely_on_holdRemind'),
            'hold_reminder_startDelay' => array('label' => 'Reminder Start Delay:', 'type' => 'input', 'class' => 'dependent_positive rely_on_holdRemind')
        ),
        'Provisioning' => array(
            'checkSync_alwaysReboot' => array('label' => 'Check Sync Reboot', 'type' => 'checkbox')
        )
    );

    public static $lineParameterMappings = array(
        'General' => array (
            'lineKeys' => array('label' => 'Num Line Keys:', 'type' => 'input'),
            'callsPerLineKey' => array('label' => 'Calls Per Line:', 'type' => 'input'),
            'type' => array('label' => 'Private Line', 'type' => 'checkbox', 'value' => 'private', 'unchecked' => 'shared'),
            'bargeInEnabled' => array('label' => 'Barge in Enabled', 'type' => 'checkbox'),
            'acd_login_logout' => array('label' => 'ACD Login/Logout', 'type' => 'checkbox', 'class' => 'dependent_positive rely_on_acdLoginOut'),
            'acd_agent_available' => array('label' => 'ACD Agent Available', 'type' => 'checkbox', 'class' => 'dependent_positive rely_on_avalAgent'),
        ),
        'Call' => array (
            'autoOffHook_enabled' => array('label' => 'Auto Off-Hook', 'type' => 'checkbox', 'class' => 'determinant agent_for_autoOff{line}'),
            'autoOffHook_contact' => array('label' => 'Auto Off-Hook Contact:', 'type' => 'input', 'class' => 'dependent_positive rely_on_autoOff{line}'),
            'missedCallTracking_enabled' => array('label' => 'Missed Call Tracking', 'type' => 'checkbox'),
            'serverMissedCall_enabled' => array('label' => 'Server Missed Call', 'type' => 'checkbox'),
            'digitmap_timeOut' => array('label' => 'Digit Timeout:', 'type' => 'input'),
        ),
        'Call Diversion' => array(
            'fwd_enabled' => array('label' => 'Allow CID Based Forwarding', 'type' => 'checkbox'),
            'autoOnSpecificCaller' => array('label' => 'Allow Forwarding', 'type' => 'checkbox'),
            'shareDisabled' => array('label' => 'Disabled On Shared', 'type' => 'checkbox'),
            'divert_contact' => array('label' => 'Diversion Contact:', 'type' => 'input'),
            'busy_enabled' => array('label' => 'On Busy', 'type' => 'checkbox', 'class' => 'determinant agent_for_busy{line}'),
            'busy_contact' => array('label' => 'Busy Contact:', 'type' => 'input', 'class' => 'dependent_positive rely_on_busy{line}'),
            'dnd_enabled' => array('label' => 'On Do-Not-Disturb', 'type' => 'checkbox', 'class' => 'determinant agent_for_dnd{line}'),
            'dnd_contact' => array('label' => 'Do-Not-Disturb Contact:', 'type' => 'input', 'class' => 'dependent_positive rely_on_dnd{line}'),
            'noanswer_enabled' => array('label' => 'On No Answer', 'type' => 'checkbox', 'class' => 'determinant agent_for_noans{line}'),
            'noanswer_contact' => array('label' => 'No Answer Contact:', 'type' => 'input', 'class' => 'dependent_positive rely_on_noans{line}'),
            'noanswer_timeout' => array('label' => 'No Answer Timeout:', 'type' => 'input', 'class' => 'dependent_positive rely_on_noans{line}'),
        )
    );

    public function index()
    {
    }
    /**
     * Setup the subview for the address plugin
     */
    public function update()
    {
        $subview = new View('PolycomDriver/update');

        // Create a list of sip devices (polycoms are sip phones)
        $devices[] = 'None';
        $devices+= sipdevices::getSipEndpoints();
        $subview->devices = $devices;

        // Load all the phone parameters
        $subview->phone = $this->driver->getParameters();
        $subview->phone['parameters']+= $this->driver->getMarkers('phone', FALSE);
        
        $subview->phoneParameterMappings = self::$phoneParameterMappings;

        // Load all the lines and their settings to the view
        $subview->lineCount = $this->driver->lineCount();
        $subview->lines = $this->driver->getLines();
        $line_defaults = $this->driver->getMarkers('line', FALSE);
        for ($lineAppearance = 0; $lineAppearance < $subview->lineCount; $lineAppearance++) {
            if (isset($subview->lines[$lineAppearance])) {
                $subview->lines[$lineAppearance]['parameters']+= $line_defaults;
            } else {
                $subview->lines[$lineAppearance]['parameters'] = $line_defaults;
            }
        }
        $subview->lineParameterMappings = self::$lineParameterMappings;
        
        // Add our view to the main application
        $this->views[] = $subview;
    }
    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();
        // Sanity check on base
        if (!$base) return FALSE; // Nothing to do here.
        // Sanity check on driver
        if (!isset($this->driver) || !is_object($this->driver)) return FALSE;
        // Save the new phone parameters
        $phoneDefaults = $this->driver->getMarkers('phone', FALSE);
        $lineDefaults = $this->driver->getMarkers('line', FALSE);
        $base->parameters = $_POST['phone']['parameters'];
        // Synchronize the endpointlines with lines
        $lineNumber = 0;
        foreach($_POST['lines'] as $line) {
            // Test if there is an existing assignment
            if (isset($base->EndpointLine[$lineNumber])) {
                $endpointLine = $base->EndpointLine[$lineNumber];
                if ($line['device_id'] == 0) {
                    kohana::log('debug', "Removing endpoint line " . $lineNumber . " from " . $this->driver->mac);
                    $endpointLine->delete();
                    $lineNumber++;
                    continue;
                } else if ($line['device_id'] != $endpointLine['device_id']) {
                    kohana::log('debug', "Updating endpoint line " . $line['device_id'] . "(" . $lineNumber . ") for " . $this->driver->mac);
                    $endpointLine->device_id = $line['device_id'];
                }
                if (!empty($line['parameters'])) {
                    $endpointLine->parameters = array_diff_assoc($line['parameters'], $lineDefaults);
                }
            } else {
                if ($line['device_id'] != 0) {
                    kohana::log('debug', "Adding endpoint line " . $line['device_id'] . "(" . $lineNumber . ") on " . $this->driver->mac);
                    $endpointLine = new EndpointLine();
                    $endpointLine->device_id = $line['device_id'];
                    $endpointLine->line_appearance = $lineNumber;
                    $endpointLine->parameters = $line['parameters'];
                    $base->EndpointLine[] = $endpointLine;
                }
            }
            $lineNumber++;
        }
        return TRUE;
    }
}
