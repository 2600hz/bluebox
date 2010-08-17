<div id="timeofday_update_header" class="txt-center update timeofday module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="timeofday_update_form" class="update timeofday">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Route'); ?>

        <div class="field">
        <?php
            echo form::label('timeofday[name]', 'Route Name:');
            echo form::input('timeofday[name]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Time Range'); ?>

        <div style="text-align: center; width: 100%;">

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[mon]', 'Monday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[mon]', 'class' => 'time_selector')); ?>

            </div>

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[tue]', 'Tuesday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[tue]', 'class' => 'time_selector')); ?>

            </div>

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[wen]', 'Wednesday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[wen]', 'class' => 'time_selector')); ?>

            </div>

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[thur]', 'Thursday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[thur]', 'class' => 'time_selector')); ?>

            </div>

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[fri]', 'Friday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[fri]', 'class' => 'time_selector')); ?>

            </div>

            <div style="display:inline; margin:0 10px;">
                <?php echo form::label('timeofday[sat]', 'Saturday'); ?>
                <?php echo form::checkbox(array('name' => 'timeofday[sat]', 'class' => 'time_selector')); ?>
            </div>

            <div style="display:inline; margin:0 10px;">

                <?php echo form::label('timeofday[sun]', 'Sunday'); ?>

                <?php echo form::checkbox(array('name' => 'timeofday[sun]', 'class' => 'time_selector')); ?>

            </div>
        </div>

        <div style="text-align: center; margin: 20px 0 40px 0;">

        <?php
            echo '<div style="width:750px; margin: 0 auto;">' .form::input(array('name' => 'timeofday[time]', 'type' => 'slider')) .'</div>';
        ?>

        </div>

        <?php echo form::open_section('During this Range'); ?>
            
            <div class="field" style="text-align: center;">

                <span id="time_range_text">On Moday through Friday between the hours of 07:00 and 17:00,<br /> route calls to a</span>

                <?php
                    if (isset($timeofday['during_number_id']))
                    {
                        $selectedClass = numbering::getAssignedPoolByNumber($timeofday['during_number_id']);
                    }
                    else
                    {
                        $selectedClass = NULL;
                    }

                    echo numbering::poolsDropdown(array(
                            'name' => 'during_class_type',
                            'forDependent' => TRUE
                        ), $selectedClass
                    );

                    echo " named ";

                    echo numbering::numbersDropdown(array(
                        'id' => 'timeofday_during',
                        'name' => 'timeofday[during_number_id]',
                        'useNames' => TRUE,
                        'optGroups' => FALSE,
                        'forDependent' => TRUE
                    ), isset($timeofday['during_number_id']) ? $timeofday['during_number_id'] : NULL);

                    jquery::addQuery('#timeofday_during')->dependent('{ parent: \'during_class_type\', group: \'common_class\' }');
                ?>

            </div>

        <?php echo form::close_section(); ?>

        <?php echo form::open_section('Outside this Range'); ?>

            <div class="field" style="text-align: center;">

                During all other times route calls to a

                <?php
                    if (isset($timeofday['outside_number_id']))
                    {
                        $selectedClass = numbering::getAssignedPoolByNumber($timeofday['outside_number_id']);
                    }
                    else
                    {
                        $selectedClass = NULL;
                    }

                    echo numbering::poolsDropdown(array(
                            'name' => 'outside_class_type',
                            'forDependent' => TRUE
                        ), $selectedClass
                    );

                    echo " named ";

                    echo numbering::numbersDropdown(array(
                        'id' => 'timeofday_outside',
                        'name' => 'timeofday[outside_number_id]',
                        'useNames' => TRUE,
                        'optGroups' => FALSE,
                        'forDependent' => TRUE
                    ), isset($timeofday['outside_number_id']) ? $timeofday['outside_number_id'] : NULL);

                    jquery::addQuery('#timeofday_outside')->dependent('{ parent: \'outside_class_type\', group: \'common_class\' }');
                ?>

            </div>

        <?php echo form::close_section(); ?>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>

<?php jquery::addPlugin(array('dependent')); ?>

<script type="text/javascript" charset="utf-8">

    function calculateHours( value ){

        var hours = Math.floor( value / 60 );

        var mins = ( value - hours*60 );

        return (hours < 10 ? "0"+hours : hours) + ":" + ( mins == 0 ? "00" : mins );

    }

    $("#timeofday_time").slider({
        from: 0,
        to: 1430,
        step: 15,
        dimension: '',
        scale: ['00:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00', '8:00',
                '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00',
                '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '00:00'],
        limits: false,
        calculate: calculateHours,
        onstatechange: function () { $('.time_selector').trigger('change'); }
    });

    $('.time_selector').change(function () {

        var day;

        var days = ["timeofday_mon", "timeofday_tue", "timeofday_wen", "timeofday_thur", "timeofday_fri", "timeofday_sat", "timeofday_sun"];

        var lastFound = NaN;

        var possibleGroup = NaN;

        var text = "";

        for (day in days) {

            if ($("#" + days[day] + ":checked").length) {

                if (lastFound = NaN || lastFound != day-1) {

                    possibleGroup = day;

                    if (text.length) {

                        text += ", ";

                    }

                    text += day;

                }

                if (day == days.length-1) {

                    if (possibleGroup != NaN && day - possibleGroup > 1) {

                        text += " through " + day;

                    } else if (possibleGroup != NaN && day - possibleGroup > 0) {

                        text += ", " + day;

                    }

                }

                lastFound = day;

            } else {

                if (lastFound == day-1) {

                    if (possibleGroup != NaN && day - possibleGroup - 1 > 1) {

                        text += " through " + (day - 1);

                    } else if (possibleGroup != NaN && day - possibleGroup - 1 > 0) {

                        text += ", " + (day - 1);

                    }

                }

            }

        }

        if (text.length) {

            text = "On " + text;

            text = text.replace(/0/, 'Monday');

            text = text.replace(/1/, 'Tuesday');

            text = text.replace(/2/, 'Wednesday');

            text = text.replace(/3/, 'Thursday');

            text = text.replace(/4/, 'Friday');

            text = text.replace(/5/, 'Saturday');

            text = text.replace(/6/, 'Sunday');

            text = text.replace(/(.*),(.*)$/, '$1 and$2');

            text += " between the hours of ";

        } else {

            text = "Between the hours of ";

        }

        var hours = $('#timeofday_time').val().split(";");
       
        $('#time_range_text').html(text + calculateHours(hours[0]) + " and " + calculateHours(hours[1]) + ",<br /> route calls to a ");

    }).trigger('change');

</script>
