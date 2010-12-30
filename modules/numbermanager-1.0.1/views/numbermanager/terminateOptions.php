<style>
    /* Vertical Tabs
    ----------------------------------*/
    .ui-tabs-vertical { width: 55em; }
    .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
    .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
    .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
    .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
    .ui-tabs-vertical .ui-tabs-panel { padding: 0 1em; float: right; width: 40em;}
</style>

<?php echo form::open_section('No Answer'); ?>

    <?php
        $baseName = 'number';

        if (isset($number['number_id']))
        {
           $baseName .= $number['number_id'];
        }

        $accordionId = 'no_answer_accordion';

        if (isset($number['number_id']))
        {
           $accordionId .= $number['number_id'];
        }
    ?>

    <div id="<?php echo $accordionId; ?>" class="no_answer_accordion">

        <h3>
            
            <a href="#" rel="hangup"><?php echo __('Hangup'); ?></a>
            
        </h3>

        <div style="text-align: center;">

            <?php echo __('If this call is not answered hangup.'); ?>

        </div>


        <h3>

            <a href="#" rel="voicemail"><?php echo __('Send to Voicemail'); ?></a>

        </h3>

        <div style="text-align: center;">

            If this call is not answered direct the caller to the voicemail box 

            <?php
                echo form::dropdown(
                    $baseName .'[dialplan][terminate][voicemail]',
                    Voicemails::provideNumberTerminators(),
                    isset($number['dialplan']['terminate']['voicemail']) ? $number['dialplan']['terminate']['voicemail'] : NULL
                );
            ?>

        </div>


        <h3>

            <a href="#" rel="transfer">Transfer</a>

        </h3>

        <div style="text-align: center;">

            <?php echo __('If this call is not answered transfer the caller to'); ?>

            <?php
                if (isset($number['dialplan']['terminate']['transfer'])) {

                    $selectedClass = numbering::getAssignedPoolByNumber($number['dialplan']['terminate']['transfer']);

                } else {

                    $selectedClass = NULL;

                }

                echo numbering::poolsDropdown($baseName .'_transfer_class', $selectedClass);

                echo __(' named ');

                echo numbering::numbersDropdown(array(
                    'id' => $baseName .'_targets',
                    'name' => $baseName .'[dialplan][terminate][transfer]',
                    'useNames' => TRUE,
                    'optGroups' => FALSE
                ), isset($number['dialplan']['terminate']['transfer']) ? $number['dialplan']['terminate']['transfer'] : NULL);
            ?>

        </div>

    </div>

    <?php
        $action = 'hangup';

        if (isset($number['dialplan']['terminate']['action']))
        {
            $action = $number['dialplan']['terminate']['action'];
        }
    ?>

    <input type="hidden" value="<?php echo empty($action) ? 'hangup' : $action; ?>" name="<?php echo $baseName; ?>[dialplan][terminate][action]" id="<?php echo $baseName; ?>_terminate_action"/>

<?php echo form::close_section(); ?>

<?php jquery::addPlugin(array('accordion', 'dependent')); ?>

<?php javascript::codeBlock(); ?>

    var terminateAction = '<?php echo $action; ?>';

    var actionIndex = $('#<?php echo $accordionId; ?> h3 a').index($('#<?php echo $accordionId; ?> h3 a[rel=' + terminateAction + ']'));

    $('#<?php echo $accordionId; ?>').accordion({ autoHeight: false, active: actionIndex });

    $('#<?php echo $accordionId; ?>').bind('accordionchange', function(event, ui) {

        var terminateAction = ui.newHeader.find('a').attr('rel');

        $('#<?php echo $baseName; ?>_terminate_action').val(terminateAction);

    });

    $('#<?php echo $baseName; ?>_targets').dependent({ parent: '<?php echo $baseName; ?>_transfer_class', group: 'common_class' });

<?php javascript::blockEnd(); ?>
