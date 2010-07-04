<div id="ajax_message_reciever" class="hide"></div>

<div id="packagexmanager_packages_header" class="modules packagemanager module_header">

    <h2><?php echo __('Package Manager'); ?></h2>

</div>

<div id="packagemanager_packages_form" class="packages packagemanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Packages'); ?>

    <?php foreach ($catalog as $packageName => $package) : extract($package); ?>

        <?php echo form::open_fieldset(); ?>
    
        <div class="package_wrapper">
            
            <div id="legend_<?php echo $packageName; ?>" class="legend packagemanager index module">

                <span class="module_actions">

                    <?php echo html::anchor('packagemanager/verify/' .$packageName, __('Verify'), array('class' => 'ajaxLink')); ?>

                    <?php echo packagemanager::dropdown('operations[' .$packageName .']', $package, empty($_POST['operations'][$packageName]) ? NULL : $_POST['operations'][$packageName]); ?>

                </span>

                <span>

                    <?php echo $displayName; ?><span class="details" style="padding-left: 25px;">(click for details)</span>

                </span>

            </div>

            <div class="module_messages">

                <?php foreach($messages as $type => $messageList): ?>

                    <?php if (empty($messageList[$packageName])) $messageList[$packageName] = array(); ?>

                    <div id ="<?php echo strtolower($packageName .'_' .$type); ?>" class="
                        <?php echo empty($messageList[$packageName]) ? 'hide' : ''; ?>
                        <?php echo $type; ?>_message
                        <?php echo $packageName; ?>_message packagemanager index module">

                        <?php if (isset($error) && $type == 'ok') : ?>

                            <?php echo __('Pending'); ?>

                        <?php elseif ($type == 'ok') : ?>

                            <?php echo __('Complete'); ?>

                        <?php else : ?>

                            <?php echo __(ucfirst($type)); ?>

                        <?php endif; ?>

                        <ul class="<?php echo $type; ?>_list packagemanager index module">

                        <?php foreach($messageList[$packageName] as $message): ?>

                            <li><?php echo $message; ?></li>

                        <?php endforeach; ?>

                        </ul>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="module_parameters">

                <?php foreach ($displayParameters as $parameter) : ?>

                    <?php if (empty($$parameter)) continue; ?>

                    <div id="<?php echo strtolower($packageName .'_' . $parameter); ?>" class="parameter parameter_<?php echo $parameter; ?>">

                        <span class="parameter_label"><?php echo __(ucfirst($parameter)); ?></span>

                        <span class="parameter_value"><?php echo $$parameter; ?></span>

                    </div>

                <?php endforeach; ?>
                
                <hr>

            </div>

        </div>

        <?php echo form::close_fieldset(); ?>
    
    <?php endforeach; ?>

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