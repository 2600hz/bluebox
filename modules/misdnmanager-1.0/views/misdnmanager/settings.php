<div id="misdnmanager_settings_header" class="txt-center settings nisdnmanager tab_header">
    <h2><?php echo __('mISDN Driver Settings'); ?></h2>
</div>

<?php message::render(); ?>

<div id="misdnmanager_settings_form" class="txt-left form settings misdnmanager">
<?php
    echo form::open();

    echo form::open_fieldset();
    echo form::legend('DSP');
    

    echo form::label('misdnsetting[dsp_debug]', 'Debug Level:');
    echo form::dropdown('misdnsetting[dsp_debug]', array(0 => '0 - disabled', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'), $misdnsetting->dsp_debug);
    
    echo form::label('misdnsetting[dsp_poll]', 'Poll Value:');
    echo form::dropdown('misdnsetting[dsp_poll]', array(32 => '32', 64 => '64', 128 => '128', 256 => '256'), $misdnsetting->dsp_poll);
//    echo form::input('misdnsetting[dsp_poll]', $misdnsetting->dsp_poll);
    
    echo html::br();
    
    echo form::label('misdnsetting[dsp_options]', 'Options:');
    echo form::input('misdnsetting[dsp_options]', $misdnsetting->dsp_options);
    
    echo form::label('misdnsetting[dsp_dtmfthreshold]', 'DTMF Threshold:');
    echo form::input('misdnsetting[dsp_dtmfthreshold]', $misdnsetting->dsp_dtmfthreshold);
        
    echo form::close_fieldset();
    

    echo form::open_fieldset();
    echo form::legend('hfcmulti');

    echo form::label('misdnsetting[hfcmulti_debug]', 'Debug Level:');
//    echo form::dropdown(array('vendor' => 'card[card_vendor_id]', 'id' => 'card_vendor_id'), $vendors);
    echo form::dropdown('misdnsetting[hfcmulti_debug]', array(0 => '0 - disabled', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5), $misdnsetting->hfcmulti_debug);

    echo form::label('misdnsetting[hfcmulti_poll]', 'Poll Value:');
    echo form::dropdown('misdnsetting[hfcmulti_poll]', array(32 => '32', 64 => '64', 128 => '128', 256 => '256'), $misdnsetting->hfcmulti_poll);
    
//    echo html::br();
    
    echo form::label('misdnsetting[hfcmulti_pcm]', 'PCM:');
    echo form::input('misdnsetting[hfcmulti_pcm]', $misdnsetting->hfcmulti_pcm);
    
    echo form::close_fieldset();
    
    
    echo form::open_fieldset();
    echo form::legend('Devnode');
    
    echo form::label('misdnsetting[devnode_user]', 'User:');
    echo form::input('misdnsetting[devnode_user]', $misdnsetting->devnode_user);
    
    echo form::label('misdnsetting[devnode_group]', 'Group:');
    echo form::input('misdnsetting[devnode_group]', $misdnsetting->devnode_group);
    
    echo form::label('misdnsetting[devnode_mode]', 'Mode:');
    echo form::input('misdnsetting[devnode_mode]', $misdnsetting->devnode_mode);
    
    echo form::close_fieldset();
    
    
    echo form::open_fieldset();
    echo form::legend('Config Files');
    
    echo form::label('misdnsetting[misdn_conf_file]', 'Main Configuration File:');
    echo form::input('misdnsetting[misdn_conf_file]', $misdnsetting->misdn_conf_file);
    
    echo html::br();
    
    echo form::label('misdnsetting[asterisk_misdn_conf_file]', 'chan_misdn Configuration File:');
    echo form::input('misdnsetting[asterisk_misdn_conf_file]', $misdnsetting->asterisk_misdn_conf_file);
    
    echo form::close_fieldset();
    

    if (isset($views)) {
        echo subview::render($views);
    }


    echo form::open_fieldset(array('class' => 'buttons'));

    echo form::submit('submit', 'Save');

    echo form::close_fieldset();

    echo form::close();
    
    echo '</div>';
    
//    jquery::addPlugin('blockUI');
//    jquery::addPlugin('selectbox');
//    jquery::addQuery('#card_vendor_id')->change('function () {
//        $.blockUI({ message: \'<h1>' .__('Please Wait...') .'</h1>\' });
//        modelDrop = $(\'#card_model_id\');
//        modelDrop.removeOption(/./);
//        modelDrop.ajaxAddOption(\'' .url::site('misdnmanager/jsonmodels') .'\', {\'id\' : $(this).val() }, false);
//    }');
//    jquery::addQuery('')->ajaxStop('$.unblockUI');
    //selectedValues()
