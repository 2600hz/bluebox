<?php echo form::open_section('Incoming Context'); ?>
    <div class="field">
    <?php
        echo form::label('trunk[context_id]', 'Default Incoming Context:');
        echo numbering::selectContext('trunk[context_id]', $trunk['context_id']);
    ?>
    </div>
<?php echo form::close_section(); ?>