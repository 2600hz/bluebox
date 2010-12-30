<?php echo form::open_section('Call Recording'); ?>

    <div class="field">
    <?php
        echo form::label('callrecord[inbound]', 'Record Inbound Calls?');
        echo form::checkbox('callrecord[inbound]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('callrecord[outbound]', 'Record Outbound Calls?');
        echo form::checkbox('callrecord[outbound]');
    ?>
    </div>

<!--    <div class="field">
    <?php
        echo form::label('callrecord[path]', 'Sub-folder for Recordings:');
        echo form::input('callrecord[path]');
    ?>
    </div>-->

<!--    <div class="field">
    < ?php
        echo form::label('callrecord[namingformat]', 'Name files by...');
        echo form::dropdown('callrecord[namingformat]', array('datetime' => 'Date & Time (YYYYMMDD-HHMMSS)',
                                                              'datetimecallid' => 'Date & Time + CallID (YYYYMMDD-HHMMSS-CallID',
                                                              'datetimetofrom' => 'Date & Time + To & From Number (YYYYMMDD-HHMMSS-To-From)',
                                                              'datetimecallidtofrom' => 'Date & Time + CallID + To & From Number (YYYYMMDD-HHMMSS-CallID-To-From)'));
    ?>
    </div>-->

<?php echo form::close_section(); ?>
