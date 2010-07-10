<div id="ajax_message_reciever" class="hide"></div>

<div id="packagexmanager_packages_header" class="modules packagemanager module_header">

    <h2><?php echo __('Package Manager'); ?></h2>

</div>

<div id="packagemanager_packages_form" class="packages packagemanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Installed'); ?>

    <?php foreach ($catalog as $packageName => $package) : //extract($package); ?>

        <?php if ($package['packageStatus'] != 'installed') continue; ?>

        <?php echo form::open_fieldset(); ?>

            <?php echo new View('packagemanager/package', arr::merge($package, array('messages' => $messages, 'displayParameters' => $displayParameters))); ?>

        <?php echo form::close_fieldset(); ?>
    
    <?php endforeach; ?>

    <?php echo form::close_fieldset(); ?>

    <?php echo form::open_section('Disabled'); ?>

    <?php foreach ($catalog as $packageName => $package) : //extract($package); ?>

        <?php if ($package['packageStatus'] != 'disabled') continue; ?>

        <?php echo form::open_fieldset(); ?>

            <?php echo new View('packagemanager/package', arr::merge($package, array('messages' => $messages, 'displayParameters' => $displayParameters))); ?>

        <?php echo form::close_fieldset(); ?>

    <?php endforeach; ?>

    <?php echo form::close_fieldset(); ?>

    <?php echo form::open_section('Uninstalled'); ?>

    <?php foreach ($catalog as $packageName => $package) : //extract($package); ?>

        <?php if (($package['packageStatus'] == 'installed') or ($package['packageStatus'] == 'disabled')) continue; ?>

        <?php echo form::open_fieldset(); ?>

            <?php echo new View('packagemanager/package', arr::merge($package, array('messages' => $messages, 'displayParameters' => $displayParameters))); ?>

        <?php echo form::close_fieldset(); ?>

    <?php endforeach; ?>

    <?php echo form::close_fieldset(); ?>

    <div class="buttons form_bottom">

        <?php echo html::anchor('packagemanager/repair_all', __('Repair All'), array('class' => 'ajaxLink repair_all')); ?>

        <span style="padding:5px">&nbsp;</span>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Update'); ?>

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