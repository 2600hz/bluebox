<?php defined('SYSPATH') or die('No direct access allowed.');?>
        <?php
        if (count($queueStatus) == 0)
                echo 'No Calls...';
        else
        { ?>
        <table id="active_table" name="active_table" class="active_table">
                <tr id="active_table_header_row" name="active_table_header_row" class="active_table_header_row">
                        <?php
                        foreach ($status_fields as $fieldname => $label)
                        {
                                echo '<th id="active_table_header_column_' . $fieldname . '" name="active_table_header_column_' . $fieldname . '" class="active_table_header_column">' . $label . '</th>';
                        }
                        ?>
                        <th>Actions</th>
                </tr>
                <?php
                $rowcount = 0;
                foreach ($queueStatus as $uuid => $channeldata)
                {
                        $rowcount++;
                        echo '<tr id="active_table_data_row_' . $uuid . '" name="active_table_data_row_' . $uuid . '" class="channel active_table_data_row_';
                        if ($rowcount % 2 == 0)
                                echo 'even';
                        else
                                echo 'odd';
                        echo '">';
                        foreach ($status_fields as $fieldname => $label)
                        {
                                echo '<td id="active_table_data_column_' . $fieldname . '" name="active_table_data_column_' . $fieldname . '" class="active_table_data_column">' . $channeldata[$fieldname] . '</td>';
                        }
                        echo '<td id="active_table_action_column" name="active_table_action_column" class="active_table_action_column">';
							echo callmanagerHelper::getLink('hangup', array($uuid));
							echo callmanagerHelper::getLink('transfer', array($uuid));
							echo callmanagerHelper::getLink('hold', array($uuid));
							echo callmanagerHelper::getLink('park', array($uuid));
							echo callmanagerHelper::getLink('record', array($uuid));
							echo callmanagerHelper::getLink('monitor', array($uuid));
                        }
                        echo '</td>';
                        echo '</tr>';
                        echo '<tr id="active_table_data_row_' . $uuid . '" name="active_table_data_row_' . $uuid . '" class="channel active_table_data_row_';
                        if ($rowcount % 2 == 0)
                                echo 'even';
                        else
                                echo 'odd';
                        echo '">';
                        echo '<td id="active_table_detail_column" name="active_table_detail_column" class="active_table_detail_column" colspan="' . (count($status_fields)+1) . '">';
                        echo '<div id="active_table_row_detwrap_' . $uuid . '" name="active_table_row_detwrap_' . $uuid . '" class="detail_wrapper">';
                        echo '<div id="active_table_row_detprompt_' . $uuid . '" name="active_table_row_detprompt_' . $uuid . '" class="prompt">(click for details)</div>';
                        echo '<div id="active_table_row_details_' . $uuid . '" name="active_table_row_details_' . $uuid . '" class="detail">';
                        echo '</div>';
                        echo '</div></td></tr>';
                }?>
        </table>
</div>
<?php
                if ($showdetail)
                {?>
                <script type="text/javascript">
$(document).ready(function () {
        $('.prompt').click(function(){
                prompt = $(this);
                parameters = $(this).parent().find('.detail');
                displayed = parameters.attr('displayed');
                if (displayed == 'true') {
                        $(prompt).text('(click for details)');
                        parameters.attr('displayed', 'false');
                        parameters.hide();
                } else {
                        $(prompt).text('(click to hide details)');
                        var cellname = parameters.attr('name');
                        var uuid = cellname.slice(cellname.lastIndexOf('_')+1, cellname.length);

                        $('#callmanager_status').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
                        $.get("<?php echo url::base() ?>index.php/callcenter_supervisor/getChannelDetail/" + uuid, function(data) {parameters.html(data);});
                        $('#callmanager_status').html('');
                        parameters.attr('displayed', 'true');
                        parameters.show();
                }
        });
        php.success({"a":[],"q":[]}, true);
});
</script>
<?php
                } // end if - show detail
        }  //end if - there are calls to display
?>
<div class="clear"></div>
<div name="update_datetime" id="update_datetime">Updated: <?php echo $updated;?></div>
