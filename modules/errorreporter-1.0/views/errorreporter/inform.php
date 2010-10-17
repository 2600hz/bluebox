<div id="support_request_help_header" class="support request_help module_header">
    <h2><?php echo 'Open Trouble Ticket'; ?></h2>
</div>

<div id="support_request_help_form" class="txt-left form support request_help">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Problem Details'); ?>

        <div class="field">
        <?php
            echo form::label('report[issue]', 'I am having an issue with:');
            echo form::textarea('report[issue]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('report[while]', 'When I:');
            echo form::textarea('report[while]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('report[error]', 'I recieved the error:');
            echo form::input('report[error]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('report[contact]', 'Contact me at:');
            echo form::input('report[contact]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('report[log]', 'Send log info:');
            echo form::checkbox('report[log]', TRUE, TRUE);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Send'); ?>

    </div>

    <?php echo form::close(); ?>

</div>