<div id="ajax_message_reciever" class="hide"></div>

<div id="packagexmanager_packages_header" class="modules packagemanager module_header">

    <h2><?php echo __('Package Manager'); ?></h2>

</div>

<div id="packagemanager_packages_form" class="packages packagemanager">

    <?php echo form::open(); ?>

    <?php foreach ($catalog as $section => $packages) : ?>

        <?php echo form::open_section($section); ?>

            <?php foreach ($packages as $packageName => $package) : ?>

                <?php echo form::open_fieldset(); ?>

                    <?php echo new View('packagemanager/package', arr::merge($package, array('packageName' => $packageName, 'messages' => $messages, 'displayParameters' => $displayParameters))); ?>

                <?php echo form::close_fieldset(); ?>

            <?php endforeach; ?>

        <?php echo form::close_fieldset(); ?>

    <?php endforeach; ?>
    
    <div class="buttons form_bottom">

        <?php echo html::anchor('packagemanager/repair_all', __('Repair All'), array('class' => 'repair_all')); ?>

        <span style="padding:5px">&nbsp;</span>

        <?php echo form::confirm_button('Update'); ?>

    </div>

    <?php echo form::close(); ?>

</div>

<?php jquery::addPlugin(array('growl', 'blockUI')); ?>
<?php javascript::codeBlock(); ?>

    $(document).ajaxStart(function () {

        $.blockUI({ message: '<div class="thinking"><?php  echo __('Please Wait...'); ?></div>' })

        }).ajaxStop($.unblockUI);

    $('a.ajaxLink').click(function (e) {

        e.preventDefault();

        $('#ajax_message_reciever').load($(this).attr('href'), { cache: false, dataType: 'html' });

    });
    
    $('.module_actions').click(function (event) { event.stopPropagation(); });

    $('.module').click(function(){

        details = $(this).parent().find('.details');

        parameters = $(this).parent().find('.module_parameters');

        displayed = parameters.attr('displayed');

        if (displayed == 'true') {

            $(details).text('(click for details)');

            parameters.attr('displayed', 'false');

            parameters.hide();

        } else {

            $(details).text('(click to hide details)');

            parameters.attr('displayed', 'true');

            parameters.show();

        }

    });
    
<?php javascript::blockEnd(); ?>