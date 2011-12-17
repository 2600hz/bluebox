<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<div id="faxing_header" class="txt-center settings module_header">
    <h2><?php echo _('Fax Profile'); ?></h2>
</div>
<div id="faxing_update_form" class="update">
    <?php echo form::open();
    echo form::open_section('');?>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_name]',
                    'hint' => 'Short Profile Name',
                    'help' => 'Short name for this profile. Used to identify profiles in lists, etc.'
                ),
                'Name:'
            );
            echo form::input('faxprofile[fxp_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_desc]',
                    'hint' => 'Description of fax profile',
                    'help' => 'Detailed description of what this fax profile is used for.'
                ),
				'Description:'
            );
            echo form::textarea(array('name' => 'faxprofile[fxp_desc]', 'rows' => 4, 'cols' => 50));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_default]',
                    'hint' => 'Use this profile as the fax defaults',
                    'help' => 'If checked, the settings from this profile will be used as the default settings. One profile must, and only one profile can have this checked; checking and saving will disable other profiles.'
                ),
				'Default Settings:'
            );
            echo form::checkbox('faxprofile[fxp_default]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_fxd_id]',
                    'hint' => 'How to handle inboung faxes',
                    'help' => 'What do you want to do with an inbound fax once it is received?'
                ),
				'Disposition:'
            );
            echo form::dropdown('faxprofile[fxp_fxd_id]', FaxDisposition::dictionary(), null, 'onChange="getDispositionForm();"');
        ?><div id="dispformstatus"></div>
        </div>
        <div class="subform" id="dispform" name="dispform">
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_ident]',
                    'hint' => 'Normally, your fax number.',
                    'help' => 'Often the fax number, a numeric only string displayed on the remote fax machine while the fax is being received.'
                ),
                'Ident:'
            );
            echo form::input('faxprofile[fxp_ident]');
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_header]',
                    'hint' => 'Your name, company name, or fax number.',
                    'help' => 'A string printed at the top of every page on the received fax, it can contain both letters and numbers.  Most often it is set to your name, your company\'s name, or your fax number.'
                ),
                'Header:'
            );
            echo form::input('faxprofile[fxp_header]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_send]',
                    'hint' => 'Use this profile for outbound email to fax',
                    'help' => 'If checked, this profile will be used for outbound faxes through the email to fax gateway. Only one profile can have this checked; checking and saving will disable other profiles.'
                ),
				'Email To Fax:'
            );
            echo form::checkbox('faxprofile[fxp_send]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_t38_mode]',
                    'hint' => 'How to handle T.38',
                    'help' => 'T.38 is a protocol for changing fax tones to data for transmission across a VOIP network.  While both ends must support T.38 for it to work, it should be negotiated automaticly.  If you are having problems, then you may have luck changing this setting.'
                ),
				'T.38 Mode:'
            );
            echo form::dropdown('faxprofile[fxp_t38_mode]', array(0 => 'Default', 1 => 'Supported', 2 => 'Requested', 3 => 'Forced'));
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_prefix]',
                    'hint' => 'Prefix added to received fax files.',
                    'help' => 'A string added to the beginning of the file name of received faxes.'
                ),
                'File Prefix:'
            );
            echo form::input('faxprofile[fxp_prefix]');
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_spool_dir]',
                    'hint' => 'Directory to use for spooling faxes.',
                    'help' => 'Directory to put fax files into while they are being received.  It must be writable by the user that Freeswitch is running as.'
                ),
                'Spool Directory:'
            );
            echo form::input('faxprofile[fxp_spool_dir]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_verbose]',
                    'hint' => 'Enable Verbose Logging.',
                    'help' => 'If enabled, be verbose writing logs.'
                ),
				'Verbose Logging:'
            );
            echo form::dropdown('faxprofile[fxp_verbose]', array(0 => 'Default', 1 => 'Yes', 2 => 'No'));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_ecm_mode]',
                    'hint' => 'Error Correction Mode',
                    'help' => 'ECM is the built in error correction that faxes use to fix errors that occur over the dial up network.'
                ),
				'ECM Mode:'
            );
            echo form::dropdown('faxprofile[fxp_ecm_mode]', array(0 => 'Default', 1 => 'Enabled', 2 => 'Disabled', 3 => 'Forced'));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_v17_mode]',
                    'hint' => 'V17 Mode',
                    'help' => 'Protocol that supports faxing at 12K and 14.4K speeds. T.30 is a legacy Group 2 fax standard.'
                ),
				'V17 Mode:'
            );
            echo form::dropdown('faxprofile[fxp_v17_mode]', array(0 => 'Default', 1 => 'Enabled', 2 => 'Disabled for T.30', 3 => 'Disabled'));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_force_caller]',
                    'hint' => 'Only allow send or receive.',
                    'help' => 'Allow this profile to only send, only receive, or send and receive (or force the profile into that mode. eg. dial in and send)'
                ),
				'Send/Receive Mode:'
            );
            echo form::dropdown('faxprofile[fxp_force_caller]', array(0 => 'Send or Receive', 1 => 'Receive Only', 2 => 'Send Only'));
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_start_page]',
                    'hint' => 'Enter -1 for default, 0 for no skip.',
                    'help' => 'Allows for pages at the beggining of the fax to be skipped. Use -1 to accept the default or 0 to skip no pages'
                ),
                'Start Page:'
            );
            echo form::input('faxprofile[fxp_start_page]', ($faxprofile->fxp_start_page == 0 ? 0 : null));
        ?>
        </div>
		<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'faxprofile[fxp_end_page]',
                    'hint' => 'Enter -1 for defaults, 0 for no limit.',
                    'help' => 'Sets a maximum fax size or allows for pages at the end of the fax to be skipped. Use -1 to accept the default or 0 for no limit.'
                ),
                'Max Pages:'
            );
            echo form::input('faxprofile[fxp_end_page]', ($faxprofile->fxp_end_page == 0 ? 0 : null));
        ?>
        </div>
	<?php
		echo form::hidden('faxprofile[fxp_id]');
        echo form::close_section();
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
		echo form::close(TRUE);
	?>
</div>
<script language="javascript">
    function getDispositionForm() {
        $('#dispformstatus').html('<img src="<?php echo url::base() . skins::getSkin();?>assets/img/thinking.gif">');
        $.post("<?php echo url::base() ?>index.php/fax/getDispositionForm", $('form').serialize(), 
                function(data) {
            		if ($.trim(data))
                	{
                		$('#dispform').html(data);
                		$('#dispform').show();
            		}
            		else
            		{
                		$('#dispform').hide();
            		} 
            		$('#dispformstatus').html("");
            	}
    	);
		
    }
</script>
<?php javascript::codeBlock(); ?>
	getDispositionForm();
<?php javascript::blockEnd();?>
