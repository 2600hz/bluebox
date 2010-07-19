<?php echo form::open_section('Route Outbound Calls Matching...'); ?>

    <?php foreach ($outboundPatterns as $key => $pattern): ?>

        <div class="field">

            <?php echo form::label('simpleroute[patterns][' . $key .']', $pattern['name']); ?>

            <?php echo form::checkbox('simpleroute[patterns][' . $key .'][enabled]'); ?>

            <span style="padding:0 5px 0;">Prepend calls with:</span>

            <?php echo form::input('simpleroute[patterns][' .$key .'][prepend]'); ?>

        </div>

    <?php endforeach; ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('Made from These Contexts...'); ?>

    <?php foreach ($contexts as $context): ?>

        <div class="field">

        <?php echo form::label('simpleroute[contexts][' .$context['context_id'] .']', $context['name']); ?>

        <?php echo form::checkbox('simpleroute[contexts][' .$context['context_id'] .']'); ?>

        </div>

    <?php endforeach; ?>

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