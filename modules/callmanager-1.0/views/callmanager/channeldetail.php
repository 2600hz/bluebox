<?php defined('SYSPATH') or die('No direct access allowed.');

if (count($channeldata) == 0)
        echo 'No Information Available...';
else
        foreach ($detailfields as $groupname => $groupfields)
        {
                echo '<div id="detail_group_' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" name="detail_group_' . str_replace(' ', '_', $groupname) . '_' . $channeldata['Unique-ID'] . '" class="detail_group">';
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
                        echo '<div class="clear"></div>';
                }
                echo '  </div>';
                echo '</div>';
        }
        echo '<div class="clear"></div>';
?>