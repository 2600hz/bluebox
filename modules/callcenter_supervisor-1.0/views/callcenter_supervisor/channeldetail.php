<?php defined('SYSPATH') or die('No direct access allowed.');

if (count($channeldata) == 0)
        echo 'No Information Available...';
else
        echo '  <div id="actions_"' . '_' . $channeldata['Unique-ID'] . '" name="actions_"' . '_' . $channeldata['Unique-ID'] . '" class="actions">';
                echo '<a id="hangup_' . $channeldata['Unique-ID'] . '" name="hangup" value="hangup" class="button_red channel_action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/hangup/' . $channeldata['Unique-ID'] . '"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/hangup.png" title="Hangup" alt="Hangup"/></a>';
                echo '<a id="transfer_' . $channeldata['Unique-ID'] . '" name="transfer" value="transfer" class="button_blue channel_action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/transfer/' . $channeldata['Unique-ID'] . '"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/transfer.png" title="Transfer" alt="Transfer"/></a>';
                echo '<a id="park_' . $channeldata['Unique-ID'] . '" name="park" value="park" class="button_blue channel_action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/park/' . $channeldata['Unique-ID'] . '"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/park.png" title="Park" alt="Park"/></a>';
                echo '<a id="record_' . $channeldata['Unique-ID'] . '" name="record" value="record" class="button_blue channel_action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/recording/' . $channeldata['Unique-ID'] . '"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/record.png" title="Recording" alt="Recording"/></a>';
                echo '<a id="callmonitor_' . $channeldata['Unique-ID'] . '" name="callmonitor" value="callmonitor" class="button_blue agent_action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/monitor/' . $channeldata['Unique-ID'] . '"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/monitor.png" title="Monitor The Call" alt="Monitor The Call"/></a>';
        echo '  </div>';
        foreach ($detailfields as $groupname => $groupfields)
        {
                echo '<div id="detail_group_' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" name="detail_group_header' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" class="detail_group">';
                echo '  <div id="detail_group_header' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" name="detail_group_header' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" class="detail_group_header">' . $groupname . '</div>';
                echo '  <div id="detail_fields_"' . '_' . $channeldata['Unique-ID'] . '" name="detail_fields_"' . '_' . $channeldata['Unique-ID'] . '" class="detail_fields">';
                foreach ($groupfields as $fieldname => $label)
                {
                        if (isset($channeldata[$fieldname]))
                        {
                                echo '      <div id="detail_field_' . $channeldata['Unique-ID'] . '" name="detail_field_' . $channeldata['Unique-ID'] . '" class="detail_field">';
                                echo '          <div id="detail_field_label_' . $channeldata['Unique-ID'] . '" name="detail_field_label_' . $channeldata['Unique-ID'] . '" class="detail_field_label">' . $label . '</div>';
                                echo '          <div id="detail_field_data_' . $channeldata['Unique-ID'] . '" name="detail_field_data_' . $channeldata['Unique-ID'] . '" class="detail_field_data">' . $channeldata[$fieldname] . '</div>';
                                echo '      </div>';
                        }
                        echo '      <div class="clear"></div>';
                }
                echo '  </div>';
                echo '</div>';
        }
        echo '  <div class="clear"></div>';
?>