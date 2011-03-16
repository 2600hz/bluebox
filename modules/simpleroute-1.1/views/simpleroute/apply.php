<?php echo form::open_section('Route Outbound Calls Matching...'); ?>

    <?php foreach ($outboundPatterns as $pattern): ?>

        <div class="field">

            <?php echo form::label('simpleroute[patterns][' . $pattern['simple_route_id'] .']', $pattern['name']); ?>

            <?php echo form::checkbox('simpleroute[patterns][' . $pattern['simple_route_id'] .'][enabled]'); ?>

            <span style="padding:0 5px 0;">Prepend calls with:</span>

            <?php echo form::input('simpleroute[patterns][' .$pattern['simple_route_id'] .'][prepend]'); ?>

        </div>

    <?php endforeach; ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('Made from These Contexts...'); ?>

    <?php foreach ($contexts as $context): ?>

        <div class="field">

        <?php echo form::label('simpleroute[contexts][' .$context['context_id'] .']', $context['name']); ?>

        <?php
            $default = NULL;
            
            if (!empty($context['registry']['type']) AND $context['registry']['type'] == 'private')
            {
                if (!isset($simpleroute['contexts'][$context['context_id']]))
                {
                    $default = TRUE;
                }
            }
        ?>

        <?php echo form::checkbox('simpleroute[contexts][' .$context['context_id'] .']', NULL, $default); ?>

        </div>

    <?php endforeach; ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('Default Settings'); ?>

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
            echo form::label(array(
                    'for' => 'simpleroute[continue_on_fail]',
                    'hint' => 'If this route fails progress to the next'
                ),
                'Continue on fail:'
            );
            echo form::checkbox('simpleroute[continue_on_fail]');
        ?>
        </div>

<?php echo form::close_section(); ?>