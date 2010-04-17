<?php echo form::open_section('Route to a Conference Bridge...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][existing]', 'Route to a specific conference? ');
        echo form::checkbox(array('class' => 'determinant agent_for_existing', 'name' => 'number[options][existing]'));
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][conference]', 'Conference:');
        echo form::hidden('number[class_type]', 'ConferenceNumber');
        echo form::dropdown(array('class' => 'dependent_positive rely_on_existing', 'name' => 'number[foreign_id]'), $conferences);
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_conference_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Conference ' + $('.destination_conference #number_foreign_id option:selected').val() + ' (' + $('.destination_conference #number_foreign_id option:selected').text() + ')');
});
<?php javascript::blockEnd(); ?>
