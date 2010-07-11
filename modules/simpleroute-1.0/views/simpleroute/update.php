<?php echo form::open_section('Route Outbound Calls Matching...'); ?>

    <?php
        $routeType = array('local', 'international', 'emergency');
        $routeDisaply = kohana::config('simpleroute.route_disaply');
        foreach ($routeType as $route) {
            echo '<div class="field">';
            echo form::label('simpleroute[' . $route . ']', $routeDisaply[$route]);
            echo form::checkbox('simpleroute[' . $route . ']');
            echo '&nbsp;&nbsp; Prepend calls with: ';
            //echo form::label('simpleroute[' . $route . '_prepend]', 'Prepend Calls With: ');
            echo form::input('simpleroute[' . $route . '_prepend]');
            echo '</div>';
        }
    ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('Made from These Contexts...'); ?>

    <?php
        foreach ($contexts as $context) {
            echo '<div class="field">';
            echo form::label('simpleroute[SimpleRouteContext]', $context['name']);
            echo form::checkbox('simpleroute[SimpleRouteContext][][context_id]', $context['context_id'], isset($checkedSimpleRouteContext[$context['context_id']]));
            echo '</div>';
        }
    ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('With Default Outbound Caller ID...'); ?>

        <div class="field">
        <?php
            echo form::label('simpleroute[caller_id_name]', 'Caller ID Name:');
            echo form::input('simpleroute[caller_id_name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('simpleroute[caller_id_number]', 'Caller ID Number:');
            echo form::input('simpleroute[caller_id_number]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('simpleroute[area_code]', 'Local Area Code:');
            echo form::input('simpleroute[area_code]');
        ?>
        </div>

<?php echo form::close_section(); ?>
