<div id="devicemanager_update_header" class="update devicemanager module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="devicemanager_update_form" class="update devicemanager">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Device Information'); ?>

        <div class="field">
        <?php
            echo form::label(array('for' => 'device[name]',
                                   'hint' => 'Nickname for this device',
                                   'help' => 'This is a friendly nickname for your device. It is used by other pages that may utilize this device and want to show the device\'s name. It is for your reference only.'),
                             'Device Name:');
            echo form::input('device[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'device[class_type]',
                                   'hint' => 'Protocol or type of device',
                                   'help' => 'The protocol or type of device that is being configured'),
                             'Device Type:');
            echo form::dropdown('device[class_type]', array('SipDevice' => 'SIP device'));
        ?>
        </div>
    
        <div class="field">
        <?php
            echo form::label(array('for' => 'device[user_id]',
                                   'hint' => 'Which user "owns" this device',
                                   'help' => 'The user who primarily operates this device. Associated Caller ID, voicemail and other settings will be inherited from this user if not explicitly set for the device.'),
                             'Assigned to:');
            echo users::dropdown('device[user_id]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Assign Device Number(s)'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label(array(
                'for' => '_numbers[assigned][]',
                'hint' => 'Numbers that ring this destination directly',
                'help' => 'Select which numbers, in which contexts, will ring this destination directly when they are called. This is a shortcut way of mapping numbers to destinations (versus using the number manager)'
            ),'Select Number:');
            echo numbering::dropdown('DeviceNumber', $device['device_id']);
            echo numbering::nextAvaliableLink('assignDeviceNumber', 'Next Avaliable Number');
        ?>
        </div>
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Outbound Call Routing'); ?>

    <div class="field">
        <?php echo form::label(array('for' => 'device[context_id]',
                'hint' => 'Default outbound call context',
                'help' => 'This field determines the phone numbers a user can call. All phone numbers and SIP trunks associated with the selected context can be dialed by this user.<BR><BR>Note that, in most cases, the user\'s device must authenticate in order for this to work. Note that if this is not set, the context for the default interface a call is received on is used instead.'),
                'Default Context:');
        ?>
        <?php echo numbering::selectContext('device[context_id]', $device['context_id']); ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views)) {
            $order = array('general', 'identification', 'membership', 'routing', 'notification', 'features', 'other');
            if (isset($views)) {
                echo subview::renderAsSections($views, $order);
            }
        }
    ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
