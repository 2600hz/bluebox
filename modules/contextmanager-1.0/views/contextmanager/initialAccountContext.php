<?php echo form::open_section('Context Information'); ?>

    <div class="field">
    <?php
        echo form::label('context[private]', 'Create Private Context:');
        echo form::checkbox('context[private]', NULL, isset($context['private']) ? $context['private'] : TRUE);
        echo ' called ';
        echo form::input('context[private_name]', empty($context['private_name']) ? 'Outbound Routes' : $context['private_name']);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('context[public]', 'Create Public Context:');
        echo form::checkbox('context[public]', NULL, isset($context['public']) ? $context['public'] : TRUE);
        echo ' called ';
        echo form::input('context[public_name]', empty($context['public_name']) ? 'Inbound Routes' : $context['public_name']);
    ?>
    </div>

<?php echo form::close_section(); ?>