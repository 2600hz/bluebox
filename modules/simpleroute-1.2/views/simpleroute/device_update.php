<?php echo form::open_section('Tags'); ?>

    <div class="field">
    <?php
        echo form::label('device[plugins][simpleroute][tags]', 'Tags:');
        echo form::input('device[plugins][simpleroute][tags]');
    ?>
    </div>

<?php echo form::close_section(); ?>
