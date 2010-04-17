<div id="welcome_container">
    <div id="conferences_update_header" class="update conferenece module_header">
        <h2><span class="helptip"></span><?php echo __('Welcome to FreePBX v3!'); ?></h2>
    </div>

    <?php echo form::open(); ?>
    <?php echo form::open_section('Getting Started'); ?>

    <div>
        <div><?php echo __('Welcome to the next generation of FreePBX. The options below will help you get started using your system. Or use the'); ?>
            <?php echo __('navigation bar above to access all features installed on the system.'); ?></div>
    </div>
    
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Phones and Equipment'); ?>

<?php
    if (class_exists('DeviceManager_Controller')) {
?>
    <div>
        <?php echo html::anchor('devicemanager', 'Configure Phones & Equipment'); ?>
        - <?php echo __('You can start exploring your system by setting up'); ?>
        <?php echo __('phones and other equipment. If you installed FreePBX with the sample data option enabled, you already have devices configured'); ?>
        <?php echo __('and ready to go. Look up the usernames and passwords and configure your phones accordingly.'); ?>
    </div>
<?php
        echo html::br();
    }
?>

<?php
    if (class_exists('NumberManager_Controller')) {
?>
    <div>
        <?php echo html::anchor('numbermanager', 'Route Phone Numbers'); ?>
        - <?php echo __('After you setup devices and/or features, you\'ll need some way for people to reach them. Use the number manager to configure'); ?>
        <?php echo __('phone numbers that ring or otherwise access your devices or installed features'); ?>
    </div>
<?php
        echo html::br();
    }
?>

    <?php echo form::close_section(); ?>

    
    <?php echo form::open_section('Connectivity'); ?>

<?php
    if (class_exists('TrunkManager_Controller')) {
?>
    <div>
        <?php echo html::anchor('trunkmanager', 'Configure Service Providers & Trunks'); ?>
        - <?php echo __('Calling between internal phone numbers is fun, but at some point you\'ll need to access the outside world. Use this tool'); ?>
        <?php echo __('to configure external service providers for making and receiving calls to the outside phone network.'); ?>
    </div>
<?php
        echo html::br();
    }
?>

<?php
    if (class_exists('SipInterface_Controller')) {
?>
    <div>
        <?php echo html::anchor('sipinterface', 'Configure IP Addresses/Ports'); ?>
        - <?php echo __('You may want to configure one or more network interfaces on your system for sending and receiving calls. The SIP Interface'); ?>
        <?php echo __('Manager lets you specify options related to sending and receiving calls.'); ?>
    </div>
<?php
        echo html::br();
    }
?>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('Features'); ?>

<?php
    if (class_exists('Conference_Controller')) {
?>
    <div>
        <?php echo html::anchor('conference', 'Configure Conferences'); ?>
        - <?php echo __('Allow multiple people to talk to each other at the same time using the conferencing feature. You can configure conferences on your system in the conference manager.'); ?>
    </div>
<?php
        echo html::br();
    }
?>

<?php
    if (class_exists('AutoAttendant_Controller')) {
?>
    <div>
        <?php echo html::anchor('autoattendant', 'Configure Auto Attendants'); ?>
        - <?php echo __('Avoid having to answer calls for other people by setting up auto-attendants. Map keys to various devices and record a sound prompt so callers can call in and reach people directly.'); ?>
    </div>
<?php
        echo html::br();
    }
?>

    <?php echo form::close_section(); ?>

    <?php echo form::close(); ?>

</div>
