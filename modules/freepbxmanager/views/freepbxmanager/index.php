<div id="freepbxmanager_modules_header" class="modules freepbxmanager module_header">
    <h2><?php echo __('Package Manager'); ?></h2>
</div>

<div id="freepbxmanager_modules_form" class="modules freepbxmanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Modules'); ?>

    <?php foreach ($packageList as $name => $parameters): ?>
    
        <?php echo form::open_fieldset(); ?>

        <legend id="legend_<?php echo $name; ?>" class="legend freepbxmanager index module">

            <span class="module_actions">
                <?php if (!empty($parameters['updateAvaliable'])): ?>
                    <span class="field settings">
                        <?php echo html::anchor('freepbxmanager/update/' .$name, __('Update to ') .$parameters['updateAvaliable'], array('class' => 'plsWait')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['hasSettings'])): ?>
                    <span class="field settings">
                        <?php echo html::anchor('freepbxmanager/settings/' .$name, __('Settings')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowRepair'])): ?>
                    <span class="field repair">
                        <?php echo html::anchor('freepbxmanager/repair/' .$name, __('Repair'), array('class' => 'ajaxLink')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowUninstall'])): ?>
                    <span class="field uninstall">
                        <?php echo html::anchor('freepbxmanager/uninstall/' .$name, __('Uninstall'), array('class' => 'confirmAjaxLink')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowVerify'])): ?>
                    <span class="field verify">
                        <?php echo html::anchor('freepbxmanager/verify/' .$name, __('Verify'), array('class' => 'ajaxLink')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowDelete'])): ?>
                    <span class="field delete">
                        <?php echo html::anchor('freepbxmanager/delete/' .$name, __('Delete'), array('class' => 'confirmAjaxLink')); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowDisable'])): ?>
                    <span class="field enabled">
                    <?php
                        echo form::label($name, 'Enabled?');
                        echo form::checkbox($name, TRUE, $parameters['enabled']);
                    ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($parameters['allowInstall'])): ?>
                    <span class="field enabled">
                    <?php
                        echo form::label($name, 'Install?');
                        echo form::checkbox($name, TRUE, FALSE);
                    ?>
                    </span>
                <?php endif; ?>

            </span>

            <span><?php echo $parameters['displayName']; ?></span>
        </legend>

        <div class="module_messages">
            <?php if (!empty($parameters['errors'])): ?>
                <div class="fail">
                    <?php echo __('ERROR'); ?>
                    <?php echo $parameters['errors']; ?>
                </div>
            <?php endif; ?>

           <?php if (!empty($parameters['warnings'])): ?>
                <div class="warning">
                    <?php echo __('WARNING'); ?>
                    <?php echo $parameters['warnings']; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="module_parameters">
        <?php foreach ($parameters['displayParameters'] as $parameter => $value) : ?>
            <?php if (empty($value)) continue; ?>
            <div id="<?php echo $name .'_' . $parameter; ?>" class="parameter parameter_<?php echo $parameter; ?>">
                <span class="parameter_label"><?php echo __(ucfirst($parameter)); ?></span>
                <span class="parameter_value"><?php echo $value; ?></span>
            </div>

        <?php endforeach; ?>
        <div style="clear:both;"></div>
        </div>

        <?php echo form::close_fieldset(); ?>

    <?php endforeach; ?>


    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo html::anchor('freepbxmanager/repair_all', __('Repair All'), array('class' => 'ajaxLink repair_all')); ?>
        <span style="padding:5px">&nbsp;</span>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Update'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
<div id="ajax_message_reciever" class="hide"></div>

<div id="question">
    <div style="color: red;"><b><?php echo __('WARNING'); ?>: </b><?php echo __('Any data will be lost!'); ?></div>
    <h1><?php echo __('Are you sure?'); ?></h1>
    <div class="buttons">
        <?php echo form::button(array('id' => 'no', 'name' => 'no', 'class' => 'no small_red_button'), 'No'); ?>
        <?php echo form::button(array('id' => 'yes', 'name' => 'yes', 'class' => 'yes small_green_button'), 'Yes'); ?>
    </div>
</div> 

<?php
    // Facts of Life by Lazyboy - interesting lyrics
    jquery::addPlugin('blockUI', 'jqrowl');
?>

<?php javascript::codeBlock(); ?>
    $('.plsWait').click(function () {
        $.blockUI({ message: '<div class="thinking"><?php  echo __('Please Wait...'); ?></div>' });
    });

    // when an ajax query starts block the UI and ends unblock
    $(document).ajaxStart(function () {
        $.blockUI({ message: '<div class="thinking"><?php  echo __('Please Wait...'); ?></div>' })
        }).ajaxStop($.unblockUI);

    // ajax link handler
    $('a.ajaxLink').click(function (e) {
        e.preventDefault();
        $('#ajax_message_reciever').load($(this).attr('href'), { cache: false, dataType: 'html' });
    });

    // ajax link handler with confirmation
    $('a.confirmAjaxLink').click(function (e) {
        $.blockUI({ message: $('#question'), css: { width: '275px' } });
        $('#question #yes').bind('click', {href : $(this).attr('href')}, function (e) {
            e.preventDefault();
            $('#ajax_message_reciever').load(e.data.href, { cache: false, dataType: 'html' });
            $('#question #yes').unbind();
        });

        $('#question #no').click(function (e) {
            $.unblockUI();
            e.preventDefault();
        });
        e.preventDefault();
    });

    // This is the effects engine to collapse and expand the modules
    $('.module_actions').click(function (event) { event.stopPropagation(); });
    $('.module').click(function(){
        parameters = $(this).parent().find('.module_parameters');
        displayed = parameters.attr('displayed');
        if (displayed == 'true') {
            parameters.attr('displayed', 'false');
            parameters.hide();
        } else {
            parameters.attr('displayed', 'true');
            parameters.show();
        }
    });
<?php javascript::blockEnd(); ?>