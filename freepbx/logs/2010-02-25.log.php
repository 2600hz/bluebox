<?php defined('SYSPATH') or die('No direct script access.'); ?>

2010-02-25 12:58:22 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:22 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:22 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:22 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 12:58:22 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 12:58:22 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 12:58:22 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-25 12:58:23 -05:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 12:58:23 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 12:58:23 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:23 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:23 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:23 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 12:58:23 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 12:58:23 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 12:58:23 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-25 12:58:23 -05:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 12:58:23 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`first_name` AS `u__first_name`, `u`.`last_name` AS `u__last_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-25 12:58:24 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:24 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:24 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:24 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 12:58:24 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 12:58:24 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 12:58:24 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 12:58:24 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 12:58:24 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 12:58:24 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 12:58:24 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 12:58:24 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 12:58:25 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:25 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:25 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:25 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 12:58:25 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 12:58:25 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 12:58:25 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 12:58:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 12:58:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 12:58:25 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 12:58:25 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 12:58:25 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 12:58:25 -05:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Doctrine_Connection_Mysql_Exception: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'v.voicemailsettings_id' in 'field list' in file freepbx/libraries/doctrine/lib/Doctrine/Connection.php on line 1067
2010-02-25 12:58:26 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:26 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:26 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 12:58:26 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 12:58:26 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 12:58:26 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 12:58:26 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 12:58:26 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.add.view
2010-02-25 12:58:26 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 12:58:26 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:25:46 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.add.save
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.save
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record VoicemailSettings
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[86] (TelephonyRecordListener) postInsert: Queuing insert of model data stored in VoicemailSettings
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:25:46 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.success.save
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - VoicemailSettings saved!
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:25:46 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:25:46 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 13:25:46 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:25:47 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:47 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:47 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:25:47 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:25:47 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:25:47 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:25:47 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:25:47 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:25:47 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:25:47 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:25:47 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 13:25:47 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 13:25:47 -05:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Doctrine_Connection_Mysql_Exception: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'v.voicemailsettings_id' in 'field list' in file freepbx/libraries/doctrine/lib/Doctrine/Connection.php on line 1067
2010-02-25 13:30:40 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:40 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:40 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:40 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:30:40 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:30:40 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:30:40 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:30:40 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:30:41 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:30:53 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:53 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:53 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:53 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:30:53 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:30:53 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:30:53 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a __construct method
2010-02-25 13:30:53 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing disable on voicemailsettings
2010-02-25 13:30:53 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a disable method
2010-02-25 13:30:53 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[814] (FreePbx_Installer) finalize: Update Module to disable voicemailsettings
2010-02-25 13:30:53 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:30:53 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:30:53 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:30:53 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:53 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - You changes have been saved
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:30:53 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:30:58 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:30:58 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:30:58 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:30:58 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:30:58 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:30:58 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a __construct method
2010-02-25 13:30:58 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing uninstall on voicemailsettings
2010-02-25 13:30:58 -05:00 --- alert: freepbx/libraries/FreePbx_Configure.php[393] (FreePbx_Configure) uninstall: UNINSTALL TRUNCATING TABLE: voicemail_settings
2010-02-25 13:30:58 -05:00 --- error: freepbx/libraries/FreePbx_Installer.php[1039] (FreePbx_Installer) _setError: INSTALL ERROR [voicemailsettings][]: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'v.voicemailsettings_id' in 'field list'
2010-02-25 13:30:58 -05:00 --- error: freepbx/helpers/message.php[52] (message) set: default - Voicemail Support Error: <ul><li>SQLSTATE[42S22]: Column not found: 1054 Unknown column 'v.voicemailsettings_id' in 'field list'</li></ul>
2010-02-25 13:30:58 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:31:57 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:31:57 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:31:57 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:31:57 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:31:57 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:31:57 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:31:58 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:32:03 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:32:03 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:32:03 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:32:03 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:32:03 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:32:03 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:40:25 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:25 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:40:25 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:40:25 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:40:25 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:25 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:40:30 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:30 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:40:30 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:40:30 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:40:30 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:30 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a __construct method
2010-02-25 13:40:30 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a verify method
2010-02-25 13:40:30 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a preInstall method
2010-02-25 13:40:30 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing install on voicemailsettings
2010-02-25 13:40:31 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a postInstall method
2010-02-25 13:40:31 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a sanityCheck method
2010-02-25 13:40:31 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[726] (FreePbx_Installer) finalize: Adding voicemailsettings to Module
2010-02-25 13:40:31 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:40:31 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:40:31 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:40:31 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: VoicemailSettings doesn't have a completedInstall method
2010-02-25 13:40:31 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:31 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:31 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:31 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - The selected package(s) have been installed.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:31 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:40:33 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:33 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:33 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:40:33 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:40:33 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:40:33 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:40:33 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:40:33 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:33 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:33 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:33 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:40:34 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:41:34 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:34 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:34 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:34 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:41:34 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:41:34 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:41:34 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:41:34 -05:00 --- error: freepbx/libraries/FreePbx_Installer.php[1039] (FreePbx_Installer) _setError: INSTALL ERROR [freeswitch][]: This module requires Asterisk Driver to also be enabled
2010-02-25 13:41:34 -05:00 --- error: freepbx/libraries/FreePbx_Installer.php[1039] (FreePbx_Installer) _setError: INSTALL ERROR [freeswitch][]: This module requires Asterisk Driver to also be enabled
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Asterisk doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Freeswitch doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Esl doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: NetList doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: InterfaceManager doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Sofia doesn't have a __construct method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing disable on asterisk
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Asterisk doesn't have a disable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing enable on freeswitch
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Freeswitch doesn't have a enable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing enable on esl
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Esl doesn't have a enable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing enable on netlistmanager
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: NetList doesn't have a enable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing enable on sipinterface
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: InterfaceManager doesn't have a enable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[571] (FreePbx_Installer) actions: Installer processing enable on sofia
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Configure.php[483] (FreePbx_Configure) noMethodMethod: Sofia doesn't have a enable method
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[814] (FreePbx_Installer) finalize: Update Module to disable asterisk
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[794] (FreePbx_Installer) finalize: Update Module to enable freeswitch
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[794] (FreePbx_Installer) finalize: Update Module to enable esl
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[794] (FreePbx_Installer) finalize: Update Module to enable netlistmanager
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:34 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:34 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[794] (FreePbx_Installer) finalize: Update Module to enable sipinterface
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:35 -05:00 --- debug: freepbx/libraries/FreePbx_Installer.php[794] (FreePbx_Installer) finalize: Update Module to enable sofia
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Module
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Module
2010-02-25 13:41:35 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:41:35 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:35 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:35 -05:00 --- error: freepbx/helpers/dialplan.php[21] (dialplan) register: Unable to register the dialplan driver 'Freeswitch'
2010-02-25 13:41:35 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - You changes have been saved
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module address does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module asterisk does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module auth does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module callerid does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module dash does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module freeswitch does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module mediaoption does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module misdn does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module rosetta does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module simpleroute does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module sip does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module TimeOfDay does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- alert: freepbx/libraries/FreePbx_Installer.php[170] (FreePbx_Installer) listPackages: Module timezone does not have any valid navigation defined.
2010-02-25 13:41:35 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:42:05 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:42:05 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:42:05 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:42:05 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:42:05 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:42:05 -05:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Kohana_404_Exception: The page you requested, provisioner/index, could not be found. in file system/core/Kohana.php on line 811
2010-02-25 13:42:10 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:42:10 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:42:10 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:42:10 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:42:10 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:42:10 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:42:10 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:42:10 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:42:10 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:42:10 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:42:57 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:42:57 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:42:57 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:42:57 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:42:57 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:42:57 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:42:57 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 13:42:57 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 13:42:59 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:42:59 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:42:59 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:42:59 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:42:59 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:42:59 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:42:59 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:42:59 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:42:59 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:42:59 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.add.view
2010-02-25 13:42:59 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:42:59 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:43:03 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:03 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:03 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:03 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:03 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:03 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:03 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:03 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:03 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.add.save
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.save
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record VoicemailSettings
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 13:43:04 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[86] (TelephonyRecordListener) postInsert: Queuing insert of model data stored in VoicemailSettings
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:43:04 -05:00 --- debug: freepbx/libraries/Telephony.php[104] (Telephony) set: Updatng information from model "VoicemailSettings" to our telephony configuration...
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[473] (FreeSwitch) autoloadXml: For query //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]/params/param[@name="sip-forbid-register"] we're loading /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to 
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 13:43:04 -05:00 --- debug: freepbx/libraries/Telephony.php[111] (Telephony) set: Done updating information from model "VoicemailSettings".
2010-02-25 13:43:04 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[248] (FreeSwitch) save: Requesting save of section /usr/local/freeswitch/conf/directory/default.xml...
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[215] (FreeSwitch) saveSection: Saving config data to /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[227] (FreeSwitch) saveSection: Done saving config file /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:04 -05:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.success.save
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - VoicemailSettings saved!
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:04 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:04 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:43:04 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:04 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:04 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:04 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 13:43:04 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 13:43:23 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:23 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:23 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:23 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:23 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:23 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:23 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:23 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:23 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.view
2010-02-25 13:43:23 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:43:23 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:25 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:25 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.save
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.save
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record VoicemailSettings
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 13:43:25 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[67] (TelephonyRecordListener) postUpdate: Queuing update of model data stored in VoicemailSettings
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 13:43:25 -05:00 --- debug: freepbx/libraries/Telephony.php[104] (Telephony) set: Updatng information from model "VoicemailSettings" to our telephony configuration...
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[473] (FreeSwitch) autoloadXml: For query //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]/params/param[@name="sip-forbid-register"] we're loading /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to 
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 13:43:25 -05:00 --- debug: freepbx/libraries/Telephony.php[111] (Telephony) set: Done updating information from model "VoicemailSettings".
2010-02-25 13:43:25 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[248] (FreeSwitch) save: Requesting save of section /usr/local/freeswitch/conf/directory/default.xml...
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[215] (FreeSwitch) saveSection: Saving config data to /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[227] (FreeSwitch) saveSection: Done saving config file /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 13:43:25 -05:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.success.save
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - VoicemailSettings saved!
2010-02-25 13:43:25 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:25 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:25 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:43:25 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 13:43:25 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:43:26 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:43:26 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:43:26 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:43:26 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:43:26 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:43:26 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 13:43:26 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 13:52:47 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:52:47 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:52:47 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:52:47 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:52:47 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:52:47 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:52:47 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:52:47 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:52:47 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:53:19 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:53:19 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:53:19 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:53:19 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:53:19 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:53:19 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:53:19 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 13:53:19 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 13:53:20 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 13:53:20 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 13:53:20 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 13:53:20 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 13:53:20 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 13:53:20 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 13:53:20 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 14:03:07 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:07 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:07 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:07 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:07 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:07 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:07 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:03:08 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:08 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:08 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:08 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:08 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:08 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:08 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:03:08 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 14:03:11 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:11 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:11 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:11 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:11 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:11 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:11 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `l`.`location_id` AS `l__location_id`, `l`.`name` AS `l__name`, `l`.`domain` AS `l__domain` FROM `location` `l`
2010-02-25 14:03:11 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-25 14:03:13 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:13 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:13 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:13 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:13 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:13 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:13 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:13 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:13 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:13 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.add.view
2010-02-25 14:03:13 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-25 14:03:13 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:18 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:18 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:18 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:18 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:18 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.add.save
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.save
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record Location
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Location
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 14:03:18 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[86] (TelephonyRecordListener) postInsert: Queuing insert of model data stored in Location
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to Location
2010-02-25 14:03:18 -05:00 --- debug: freepbx/libraries/Telephony.php[104] (Telephony) set: Updatng information from model "Location" to our telephony configuration...
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="locations"]
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[473] (FreeSwitch) autoloadXml: For query //document/section[@name="locations"]/X-PRE-PROCESS[@cmd="set"][@freepbx="location_3"] we're loading /usr/local/freeswitch/conf/autoload_configs/locations.conf.xml
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to 
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="locations"]
2010-02-25 14:03:18 -05:00 --- debug: freepbx/libraries/Telephony.php[111] (Telephony) set: Done updating information from model "Location".
2010-02-25 14:03:18 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[248] (FreeSwitch) save: Requesting save of section /usr/local/freeswitch/conf/autoload_configs/locations.conf.xml...
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[215] (FreeSwitch) saveSection: Saving config data to /usr/local/freeswitch/conf/autoload_configs/locations.conf.xml
2010-02-25 14:03:18 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[227] (FreeSwitch) saveSection: Done saving config file /usr/local/freeswitch/conf/autoload_configs/locations.conf.xml
2010-02-25 14:03:18 -05:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.success.save
2010-02-25 14:03:18 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - Location saved!
2010-02-25 14:03:19 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:19 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:19 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:19 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:19 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:19 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:19 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `l`.`location_id` AS `l__location_id`, `l`.`name` AS `l__name`, `l`.`domain` AS `l__domain` FROM `location` `l`
2010-02-25 14:03:19 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-25 14:03:36 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:36 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:36 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:36 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:36 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:36 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:36 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:03:36 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 14:03:38 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:38 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:38 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:38 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:38 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:38 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:38 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.view
2010-02-25 14:03:38 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:38 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:39 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:39 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.save
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.save
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record VoicemailSettings
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 14:03:39 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[67] (TelephonyRecordListener) postUpdate: Queuing update of model data stored in VoicemailSettings
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 14:03:39 -05:00 --- debug: freepbx/libraries/Telephony.php[104] (Telephony) set: Updatng information from model "VoicemailSettings" to our telephony configuration...
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[473] (FreeSwitch) autoloadXml: For query //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]/params/param[@name="sip-forbid-register"] we're loading /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to 
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 14:03:39 -05:00 --- debug: freepbx/libraries/Telephony.php[111] (Telephony) set: Done updating information from model "VoicemailSettings".
2010-02-25 14:03:39 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[248] (FreeSwitch) save: Requesting save of section /usr/local/freeswitch/conf/directory/default.xml...
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[215] (FreeSwitch) saveSection: Saving config data to /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[227] (FreeSwitch) saveSection: Done saving config file /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:03:39 -05:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.success.save
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - VoicemailSettings saved!
2010-02-25 14:03:39 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:39 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:39 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:39 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:03:39 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:03:40 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:03:40 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:03:40 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:03:40 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:03:40 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:03:40 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:03:40 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 14:15:54 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:15:54 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:15:54 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:15:54 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:15:54 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:15:54 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:15:54 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:15:54 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:15:54 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:15:54 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.view
2010-02-25 14:15:54 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:15:54 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:15:56 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:15:56 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.edit.save
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.save
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/FreePbx_Record.php[84] (FreePbx_Record) save: Saving record VoicemailSettings
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/TelephonyListener.php[66] (TelephonyListener) preTransactionBegin: Instantiated our telephony driver before updating the model data.
2010-02-25 14:15:56 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/TelephonyRecordListener.php[67] (TelephonyRecordListener) postUpdate: Queuing update of model data stored in VoicemailSettings
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/TelephonyListener.php[79] (TelephonyListener) postTransactionCommit: Creating config from saved models in memory.
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/FreePbx_Record.php[237] (FreePbx_Record) setBaseSaveObject: Setting base object to VoicemailSettings
2010-02-25 14:15:56 -05:00 --- debug: freepbx/libraries/Telephony.php[104] (Telephony) set: Updatng information from model "VoicemailSettings" to our telephony configuration...
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_1"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[473] (FreeSwitch) autoloadXml: For query //document/section[@name="directory"]/domain[@name="voicemail_1"]/groups/group[@name="default"]/users/user[@id="2000"]/params/param[@name="sip-forbid-register"] we're loading /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to 
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FsDomDocument.php[68] (FsDomDocument) setXmlRoot: Setting XML root to //document/section[@name="directory"]/domain[@name="voicemail_1"]/groups/group[@name="default"]/users/user[@id="2000"]
2010-02-25 14:15:56 -05:00 --- debug: freepbx/libraries/Telephony.php[111] (Telephony) set: Done updating information from model "VoicemailSettings".
2010-02-25 14:15:56 -05:00 --- debug: freepbx/models/FreePbx_Record.php[239] (FreePbx_Record) setBaseSaveObject: Done with base object
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[248] (FreeSwitch) save: Requesting save of section /usr/local/freeswitch/conf/directory/default.xml...
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[215] (FreeSwitch) saveSection: Saving config data to /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[227] (FreeSwitch) saveSection: Done saving config file /usr/local/freeswitch/conf/directory/default.xml
2010-02-25 14:15:56 -05:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.success.save
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/message.php[50] (message) set: default - VoicemailSettings saved!
2010-02-25 14:15:56 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:15:56 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:15:56 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:15:56 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 14:15:56 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 14:15:57 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 14:15:57 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 14:15:57 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 14:15:57 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 14:15:57 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 14:15:57 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 14:15:57 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 16:08:49 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 16:08:49 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 16:08:49 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 16:08:49 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 16:08:49 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 16:08:49 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 16:08:49 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 16:08:49 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 16:08:49 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-25 16:08:50 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 16:08:50 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 16:08:50 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 16:08:50 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 16:08:50 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 16:08:50 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 16:08:50 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-25 16:08:50 -05:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-25 18:20:30 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 18:20:30 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 18:20:30 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 18:20:30 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 18:20:30 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 18:20:30 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 18:20:30 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 18:20:30 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 18:20:30 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 18:20:31 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 18:20:31 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 18:20:31 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 18:20:31 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 18:20:31 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 18:20:31 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.index.view
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->login
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->register
2010-02-25 18:20:31 -05:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.view
2010-02-25 18:20:31 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-25 18:20:38 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 18:20:38 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 18:20:38 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 18:20:38 -05:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-25 18:20:38 -05:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver Freeswitch
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call Freeswitch_CallerId_Driver::conditioning
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call Freeswitch_VoicemailSettings_Driver::prenumber
2010-02-25 18:20:38 -05:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call Freeswitch_VoicemailSettings_Driver::postnumber
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-25 18:20:38 -05:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-25 18:20:38 -05:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-25 18:20:39 -05:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
