<?php echo form::open_section('Directory Listing'); ?>

    <div class="field">
    <?php
        echo form::label('directory[group]', 'Group:');
        echo form::dropdown('directory[group]', $groupings);
    ?>
    </div>

<?php echo form::close_section(); ?>
