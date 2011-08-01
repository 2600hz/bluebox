<?php defined('SYSPATH') or die('No direct access allowed.');

?>
<div id="callcenter_settings_header" class="txt-center settings callcenter module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="callcenter_settings_update_form" class="update callcenter">
	<div class="sub_menu"><a class="qtipAjaxForm" href="/bluebox/index.php/callcenter_core/syncRunningConfig">Synchronize Configuration</a></div>
	<div class="sub_menu"><a class="qtipAjaxForm" href="/bluebox/index.php/callcenter_core/reload">Load/Reload Callcenter</a></div>

    <?php echo form::open(); ?>

    <?php echo form::open_section('Core Settings'); ?>

	<?php
	if (class_exists('OdbcManager')) {
	?>
	<div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_settings[cc_odbc_dsn]',
                    'hint' => 'DSN to use for Call Center Data',
                    'help' => 'Select an ODBC DSN to save the Call Center Data to an external database for clustering, reporting, external application integration, etc.'
                ),
                'DSN:'
            );
            echo OdbcManager::dsnSelector('callcenter_settings[cc_odbc_dsn]', empty($cc_odbc_dsn) ? NULL : $cc_odbc_dsn);
        ?>
        </div>
        <?php } ?>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_settings[cc_db_name]',
                    'help' => 'Name or path and name of SQLite DB to use for Call Center data.  Usefull for putting the DB on a Ram Drive for speed.',
                    'hint' => 'Enter a name or path/name for the SQLite DB.'
                ),
                 'SQLite DB Path/Name:'
            );
            echo form::input('callcenter_settings[cc_db_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callcenter_settings[cc_update_mode]',
                    'help' => 'If set to real time, Bluebox attempts to make configuration changes real time.  If batch is selected, the changes are attempted when the "Syncronize Configuration" button is selected.  In Freeswitch, mod_callcenter maintains its own internal state data and does not depend on the xml config.  This insures the integrity of the running system and provides protection from mistakes (like accidentally deleting a queue), but means that changes must be intentionally applied.',
                    'hint' => 'Real time or batch update.'
                ),
                 'Update Mode:'
            );
            echo form::dropdown('callcenter_settings[cc_update_mode]', array('realtime' => 'Real Time', 'batch' => 'Batch'));
        ?>
        </div>
    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

</div>