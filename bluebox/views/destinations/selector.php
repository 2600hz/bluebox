<?php echo form::open(NULL, array('id' => 'destination_selector')); ?>

    <?php if (isset($views)): ?>

        <div id="destination_selector_choices" class="field">
            <ul id="choices">
                <?php foreach ($views as $subview): ?>
                    <li id="destination_<?php echo strtolower($subview->section); ?>">
                        <?php echo $subview->section; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div style="clear:both;"> </div>

        <?php foreach ($views as $subview): ?>
            <div class="destination destination_<?php echo strtolower($subview->section); ?> hide">
                <?php echo subview::render($subview); ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <?php echo form::hidden('friendly_name'); ?>

    <div class="buttons form_bottom">
        <?php //echo form::button(array('name' => 'submit', 'class' => 'destination_submit submit small_red_button'), 'Cancel');
        ?>
        <?php echo form::button(array('name' => 'submit', 'class' => 'destination_submit submit small_green_button'), 'Confirm Destination'); ?>
    </div>

<?php echo form::close(); ?>

<?php javascript::codeBlock(); ?>
    var destinationType = '';

    $('#choices li').click(function (e) {
        $('.destination').hide();

        destinationType = this.id;
        $('.' + destinationType).fadeIn();
    });




    $('#destination_selector').submit(function (e) {
        e.preventDefault();
        subForm =
            '.' + destinationType + ' input, ' +
            '.' + destinationType + ' select, ' +
            '.' + destinationType + ' radio';

        // Ask the relevant JS to update it's friendly name for the given selection
        $(document).trigger(destinationType + '_submit');

        var friendlyName = $('form#destination_selector input[name="friendly_name"]').val();
        if (friendlyName.length > 0) {
            $('.<?php echo $dom_id; ?>').html(friendlyName + ' ');
        }

        if ($('#destination\\[<?php echo $dom_id; ?>\\]').size() > 0) {
            $('#destination\\[<?php echo $dom_id; ?>\\]').val($(subForm).serialize());
        } else {
            $('#<?php echo $dom_id; ?>').after('<input type="hidden" id="destination[<?php echo $dom_id; ?>]" name="destination[<?php echo $dom_id; ?>]" value="' + $(subForm).serialize() + '" />');
        }
        
        $('#<?php echo $dom_id; ?>').qtip("hide");
    });

<?php javascript::blockEnd(); ?>