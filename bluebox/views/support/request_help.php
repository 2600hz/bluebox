<div id="support_request_help_header" class="support request_help module_header">
    <h2><span class="helptip"></span><?php echo __('Support'); ?></h2>
</div>

<div id="support_request_help_form" class="txt-left form support request_help">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Submit Support Request'); ?>

        <div class="field">
        <?php
            echo form::label('wanted_to', 'When I was trying to:');
            echo form::textarea('wanted_to');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('issue', 'I am having an issue with:');
            echo form::textarea('issue');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Send'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
