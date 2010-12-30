<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo __('Bluebox Setup Wizard'); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/installer/assets/css/reset.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/installer/assets/css/layout.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/installer/assets/css/screen.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/installer/assets/css/forms.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/installer/assets/css/installer.css" media="screen" />
    <?php echo $js; ?>
    <?php echo html::link('skins/installer/assets/img/favicon.ico', 'icon', 'image/x-icon'); ?>
</head>
<body>
    <div class="container">

        <div class="header">
            <div class="topbar">
                <?php
                    echo html::anchor('installer/reset', __('Restart Wizard'), array(
                        'title' => 'Reset the installation wizard, and start again.',
                    ));
                ?>
                |
                <?php
                    echo html::anchor('http://www.2600hz.org', __('Wiki'), array(
                        'title' => 'Find answers on the wiki for common problems.',
                        'target' => '_blank'
                    ));
                ?>
            </div>
        </div>
        <!-- END OF HEADER -->

        <div class="installer_header">
            <!-- Logo for the page -->
            <div id="logo_container">
            <?php
                //echo html::image('skins/installer/assets/img/logo2.png', array(
                //    'alt' => 'Bluebox v3 Let freedom ring!',
                //));
            ?>
            </div>

            <h2><?php echo __($title); ?></h2>

            <div class="bluebox_desc">
            <?php
                echo __('Our free software. Your next voip system.');
            ?>
            </div>
            <?php message::render(NULL, array('growl' => FALSE, 'html' => TRUE)); ?>
        </div>

        <div class="content">

            <div class="wrapper">

                <div class="main">

                    <?php echo form::open(NULL, array('id' => 'installWizard'), array('form_token' => $formToken)); ?>

                    <?php echo $content; ?>

                    <?php if (isset($views)) echo subview::render($views); ?>

                </div>

                <div class="buttons form_bottom">
                <?php
                    if(!empty($allowNext)) {
                        echo form::button(array('name' => 'next', 'class' => 'save small_green_button'), 'Continue');
                    }
                ?>
                <?php
                    if(!empty($allowPrev)) {
                        echo form::button(array('name' => 'prev', 'class' => 'prev small_red_button'), 'Back');
                    }
                ?>
                </div>

                <?php echo form::close(); ?>
            </div>
        </div>
    </div>
    <?php javascript::renderCodeBlocks(); ?>
</body>
</html>
<?php if (Kohana::config('core.render_stats') === TRUE) { ?>
<!-- Kohana Version: {kohana_version} -->
<!-- Kohana Codename: {kohana_codename} -->
<!-- Execution Time: {execution_time} -->
<!-- Memory Usage: {memory_usage} -->
<!-- Included Files: {included_files} -->
<?php } ?>