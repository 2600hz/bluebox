<?php defined('SYSPATH') or die('No direct script access.'); ?>

2010-02-28 00:21:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 00:21:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 00:21:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 00:21:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 00:21:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 00:21:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 00:21:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 00:21:32 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 00:21:32 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 01:44:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.index.view
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->login
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->register
2010-02-28 01:44:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.view
2010-02-28 01:44:33 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 01:44:38 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:38 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:38 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:38 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:38 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:38 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.index.view
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.view
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 01:44:38 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 01:44:38 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 01:44:39 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:39 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:39 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:39 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:39 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:39 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.index.view
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.view
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 01:44:39 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`context_id` AS `c__context_id`, `c`.`name` AS `c__name` FROM `context` `c`
2010-02-28 01:44:41 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:41 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:41 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:41 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:41 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:41 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:41 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:41 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:41 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:41 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.edit.view
2010-02-28 01:44:41 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.view
2010-02-28 01:44:41 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 01:44:43 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:43 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:43 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:43 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:43 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:43 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.index.view
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.view
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 01:44:43 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 01:44:43 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 01:44:44 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 01:44:44 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 01:44:44 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 01:44:44 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 01:44:44 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 01:44:44 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.index.view
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event contextmanager.view
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 01:44:44 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`context_id` AS `c__context_id`, `c`.`name` AS `c__name` FROM `context` `c`
2010-02-28 02:14:24 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:24 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:24 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:14:24 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:24 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:24 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:24 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:14:24 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`conference_id` AS `c__conference_id`, `c`.`name` AS `c__name`, `c`.`record` AS `c__record` FROM `conference` `c`
2010-02-28 02:14:26 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:26 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:26 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:26 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:26 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:26 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 02:14:26 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 02:14:26 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 02:14:26 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:26 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:14:26 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:14:27 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:27 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:27 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:27 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:27 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:27 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 02:14:27 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 02:14:27 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 02:14:27 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:14:27 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 02:14:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:33 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:14:33 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:14:34 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:34 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:34 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:34 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:34 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:34 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:14:34 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `a`.`auto_attendant_id` AS `a__auto_attendant_id`, `a`.`name` AS `a__name`, `a`.`description` AS `a__description` FROM `auto_attendant` `a`
2010-02-28 02:14:35 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:35 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:35 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:35 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:35 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:35 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:35 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:14:35 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:14:36 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:36 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:36 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:36 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:36 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:36 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:14:36 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `r`.`ring_group_id` AS `r__ring_group_id`, `r`.`name` AS `r__name` FROM `ring_group` `r`
2010-02-28 02:14:37 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:14:37 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:14:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:14:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:14:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:14:37 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:14:37 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:14:37 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:14:37 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:14:37 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.add.view
2010-02-28 02:14:37 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 02:14:37 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:11 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:11 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:11 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:11 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:11 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:11 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:11 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:15:11 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:12 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:12 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:12 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:12 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:12 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:12 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:15:12 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `a`.`auto_attendant_id` AS `a__auto_attendant_id`, `a`.`name` AS `a__name`, `a`.`description` AS `a__description` FROM `auto_attendant` `a`
2010-02-28 02:15:14 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:14 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:14 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:14 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:14 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:14 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:14 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.add.view
2010-02-28 02:15:14 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:15:15 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:19 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:19 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:19 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:19 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:19 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:19 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:19 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:15:19 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:20 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:20 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:20 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:20 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:20 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:20 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:15:20 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `a`.`auto_attendant_id` AS `a__auto_attendant_id`, `a`.`name` AS `a__name`, `a`.`description` AS `a__description` FROM `auto_attendant` `a`
2010-02-28 02:15:26 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:26 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:26 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:26 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:26 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:26 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:26 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:15:26 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `r`.`ring_group_id` AS `r__ring_group_id`, `r`.`name` AS `r__name` FROM `ring_group` `r`
2010-02-28 02:15:29 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:29 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:29 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:29 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:29 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:29 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 02:15:30 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 02:15:30 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 02:15:30 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 02:15:30 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 02:15:30 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 02:15:30 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 02:15:30 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 02:15:30 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`name` AS `v__name`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-28 09:10:47 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:10:47 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:10:47 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:10:47 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:10:47 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:10:47 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:10:47 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:10:47 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`conference_id` AS `c__conference_id`, `c`.`name` AS `c__name`, `c`.`record` AS `c__record` FROM `conference` `c`
2010-02-28 09:10:48 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:10:48 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:10:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:10:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:10:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:10:48 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:10:48 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:10:48 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:10:48 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:10:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.add.view
2010-02-28 09:10:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 09:10:48 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:29 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:29 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:29 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:29 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:29 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:29 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 09:11:29 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 09:11:29 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 09:11:29 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:29 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:11:29 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:30 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:30 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:30 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:30 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:30 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:30 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 09:11:30 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 09:11:30 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 09:11:30 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:11:30 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 09:11:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.add.view
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->update
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->update
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook MediaOption_Plugin->view
2010-02-28 09:11:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->view
2010-02-28 09:11:32 -08:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Doctrine_Connection_Mysql_Exception: SQLSTATE[42S22]: Column not found: 1054 Unknown column 's.contact' in 'field list' in file freepbx/libraries/doctrine/lib/Doctrine/Connection.php on line 1067
2010-02-28 09:11:36 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:36 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:36 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:36 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:36 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:36 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:36 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 09:11:36 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:11:36 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 09:11:37 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:37 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:37 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:37 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:37 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:37 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:37 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:11:38 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:38 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:38 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:38 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:38 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:38 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:38 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:11:38 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `a`.`auto_attendant_id` AS `a__auto_attendant_id`, `a`.`name` AS `a__name`, `a`.`description` AS `a__description` FROM `auto_attendant` `a`
2010-02-28 09:11:46 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:46 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:46 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:46 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:46 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:46 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:46 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:11:46 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:47 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:47 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:47 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:47 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:47 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:47 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.index.view
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event ringgroup.view
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:11:47 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `r`.`ring_group_id` AS `r__ring_group_id`, `r`.`name` AS `r__name` FROM `ring_group` `r`
2010-02-28 09:11:48 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:48 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:48 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 09:11:48 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 09:11:48 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 09:11:48 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 09:11:48 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.index.view
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event voicemailsetting.view
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 09:11:48 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `v`.`voicemailsettings_id` AS `v__voicemailsettings_id`, `v`.`mailbox` AS `v__mailbox`, `v`.`name` AS `v__name`, `v`.`email_address` AS `v__email_address` FROM `voicemail_settings` `v`
2010-02-28 11:01:46 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:01:46 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:01:46 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:01:46 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:01:46 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:01:46 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 11:01:46 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 11:01:46 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 11:01:46 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:01:46 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 11:01:46 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 11:01:47 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:01:47 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:01:47 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:01:47 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:01:47 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:01:47 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 11:01:47 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 11:01:47 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 11:01:47 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 11:01:47 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 11:01:48 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:01:48 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:01:48 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:01:48 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:01:48 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:01:48 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.add.view
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->update
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->update
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook MediaOption_Plugin->view
2010-02-28 11:01:48 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->view
2010-02-28 11:01:48 -08:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Doctrine_Connection_Mysql_Exception: SQLSTATE[42S22]: Column not found: 1054 Unknown column 's.contact' in 'field list' in file freepbx/libraries/doctrine/lib/Doctrine/Connection.php on line 1067
2010-02-28 11:02:01 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:02:01 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:02:01 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 11:02:01 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:02:01 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:02:01 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:02:01 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 11:02:01 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 11:02:01 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 11:06:39 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:06:39 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:06:39 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:06:39 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:06:39 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:06:39 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:06:39 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column name as context doesnt exist in Context
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sipinterface.index.view
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sipinterface.view
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:06:39 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 11:06:40 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 11:06:40 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:06:40 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:06:40 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:06:40 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:06:40 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:06:40 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:06:40 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column name as context doesnt exist in Context
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sipinterface.index.view
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sipinterface.view
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 11:06:40 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `s`.`sipinterface_id` AS `s__sipinterface_id`, `s`.`name` AS `s__name`, `s`.`ip_address` AS `s__ip_address`, `s`.`port` AS `s__port` FROM `sip_interface` `s`
2010-02-28 11:06:44 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:06:44 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:06:44 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:06:44 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:06:44 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:06:44 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event netlistmanager.index.view
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event netlistmanager.view
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:06:44 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 11:06:44 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 11:06:45 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:06:45 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:06:45 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:06:45 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:06:45 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:06:45 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event netlistmanager.index.view
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event netlistmanager.view
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 11:06:45 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `n`.`net_list_id` AS `n__net_list_id`, `n`.`name` AS `n__name` FROM `net_list` `n`
2010-02-28 11:12:12 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:12:12 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:12:12 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:12:12 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:12:12 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:12:12 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.index.view
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.view
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:12:12 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 11:12:12 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 11:12:13 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:12:13 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:12:13 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:12:13 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:12:13 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:12:13 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.index.view
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.view
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 11:12:13 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `s`.`call_id` AS `s__call_id`, `s`.`sip_user` AS `s__sip_user`, `s`.`sip_host` AS `s__sip_host`, `s`.`contact` AS `s__contact`, `s`.`status` AS `s__status`, `s`.`user_agent` AS `s__user_agent` FROM `sip_registrations` `s`
2010-02-28 11:12:20 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 11:12:20 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 11:12:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 11:12:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 11:12:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 11:12:20 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 11:12:20 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 11:12:20 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 11:12:20 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 11:12:20 -08:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-28 11:12:21 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:20 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:20 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:20 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:20 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:20 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:20 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:20 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:20 -08:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-28 15:49:24 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:24 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:24 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:24 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:24 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:24 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:24 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:49:24 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:25 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:25 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:25 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:25 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:25 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:25 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:49:25 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`conference_id` AS `c__conference_id`, `c`.`name` AS `c__name`, `c`.`record` AS `c__record` FROM `conference` `c`
2010-02-28 15:49:30 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:30 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:30 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 15:49:30 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 15:49:30 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 15:49:30 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:30 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:30 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:30 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:30 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:30 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 15:49:31 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 15:49:31 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 15:49:31 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:49:31 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 15:49:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.add.view
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->update
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->update
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook MediaOption_Plugin->view
2010-02-28 15:49:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->view
2010-02-28 15:49:32 -08:00 --- error: system/core/Kohana.php[883] (Kohana) exception_handler: Uncaught Doctrine_Connection_Mysql_Exception: SQLSTATE[42S22]: Column not found: 1054 Unknown column 's.contact' in 'field list' in file freepbx/libraries/doctrine/lib/Doctrine/Connection.php on line 1067
2010-02-28 15:49:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 15:49:33 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 15:49:33 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 15:49:33 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:33 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:33 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:33 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:33 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:33 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.index.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->index
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->index
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sip_Plugin->index
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Sofia_Plugin->index
2010-02-28 15:49:34 -08:00 --- alert: freepbx/helpers/jgrid.php[318] (jgrid) add: Unable to locate model Sofia checking for alias on baseModel
2010-02-28 15:49:34 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Unable to locate Sofia
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->index
2010-02-28 15:49:34 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event devicemanager.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `d`.`device_id` AS `d__device_id`, `d`.`name` AS `d__name`, `d`.`class_type` AS `d__class_type`, `d`.`user_id` AS `d__user_id`, `s`.`sip_id` AS `s__sip_id`, `s`.`username` AS `s__username`, `u`.`user_id` AS `u__user_id`, `u`.`last_name` AS `u__last_name`, `u`.`first_name` AS `u__first_name` FROM `device` `d` LEFT JOIN `sip` `s` ON `d`.`foreign_id` = `s`.`sip_id` LEFT JOIN `user` `u` ON `d`.`user_id` = `u`.`user_id`
2010-02-28 15:49:34 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:34 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:34 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:34 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:34 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:34 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:34 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.index.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event conference.view
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:49:34 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `c`.`conference_id` AS `c__conference_id`, `c`.`name` AS `c__name`, `c`.`record` AS `c__record` FROM `conference` `c`
2010-02-28 15:49:51 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:51 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:51 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:51 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:51 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:51 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:51 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:51 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:52 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:52 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:52 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:52 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:52 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:52 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:52 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.index.view
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->login
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook User_Plugin->register
2010-02-28 15:49:52 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event user.view
2010-02-28 15:49:52 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:49:55 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:55 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:55 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:55 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:49:55 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:49:55 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:49:55 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:49:55 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:50:04 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:04 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:04 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:04 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:04 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:04 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:50:04 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:50:04 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:50:05 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:05 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:05 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:05 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:05 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:05 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.index.view
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:50:05 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `a`.`auto_attendant_id` AS `a__auto_attendant_id`, `a`.`name` AS `a__name`, `a`.`description` AS `a__description` FROM `auto_attendant` `a`
2010-02-28 15:50:07 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:07 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:07 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:07 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:07 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:07 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:07 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:07 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:07 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:07 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.add.view
2010-02-28 15:50:07 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 15:50:07 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:50:53 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:53 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:53 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:53 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:53 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:53 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:53 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:53 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:53 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:53 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.add.view
2010-02-28 15:50:53 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event autoattendant.view
2010-02-28 15:50:53 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:50:58 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:58 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:58 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.index.view
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.view
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:50:58 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:58 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:58 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:58 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.index.view
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event sofia.view
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:50:58 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `s`.`call_id` AS `s__call_id`, `s`.`sip_user` AS `s__sip_user`, `s`.`sip_host` AS `s__sip_host`, `s`.`contact` AS `s__contact`, `s`.`status` AS `s__status`, `s`.`user_agent` AS `s__user_agent` FROM `sip_registrations` `s`
2010-02-28 15:50:59 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:50:59 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:50:59 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:50:59 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:50:59 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:50:59 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:50:59 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:50:59 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:50:59 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:50:59 -08:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-28 15:50:59 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:51:11 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:51:11 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:51:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:51:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:51:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:51:11 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:51:11 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:51:11 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:51:11 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:51:11 -08:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-28 15:51:16 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:51:16 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:51:16 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:51:16 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:51:16 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:51:16 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:51:16 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:51:16 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:51:16 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:51:16 -08:00 --- alert: freepbx/helpers/message.php[52] (message) set: default - Failed to connect to ESL. Make sure FreeSWITCH is running...
2010-02-28 15:51:16 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:51:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:51:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:51:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:51:32 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:51:32 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:51:32 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:51:32 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.index.view
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook LocationManager_Plugin->index
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `l`.`location_id` AS `l__location_id`, `l`.`name` AS `l__name`, `l`.`domain` AS `l__domain` FROM `location` `l`
2010-02-28 15:51:32 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 15:52:02 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:52:02 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:52:02 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 15:52:02 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:52:02 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:52:02 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:52:02 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:52:02 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:52:02 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 15:52:02 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:52:02 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[853] (jgrid) _autoQuery: Adding: where(u.location_id =  1,~NULL~)
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `u`.`user_id` AS `u__user_id`, `u`.`email_address` AS `u__email_address`, `u`.`last_login` AS `u__last_login`, `u`.`first_name` AS `u__first_name`, `u`.`last_name` AS `u__last_name`, `u`.`account_type` AS `u__account_type` FROM `user` `u` WHERE `u`.`location_id` = 1
2010-02-28 15:52:02 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 15:52:11 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:52:11 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:52:11 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:52:11 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:52:11 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:52:11 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event usermanager.edit.view
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Permission_Plugin->update
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook Address_Plugin->update
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook CallerId_Plugin->update
2010-02-28 15:52:11 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event usermanager.view
2010-02-28 15:52:11 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:52:13 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:52:13 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:52:13 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:52:13 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:52:13 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:52:13 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 15:52:13 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:52:13 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 15:52:13 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 15:52:13 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 15:52:14 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 15:52:14 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 15:52:14 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 15:52:14 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 15:52:14 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 15:52:14 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 15:52:14 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 15:52:14 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/jgrid.php[853] (jgrid) _autoQuery: Adding: where(u.location_id =  1,~NULL~)
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `u`.`user_id` AS `u__user_id`, `u`.`email_address` AS `u__email_address`, `u`.`last_login` AS `u__last_login`, `u`.`first_name` AS `u__first_name`, `u`.`last_name` AS `u__last_name`, `u`.`account_type` AS `u__account_type` FROM `user` `u` WHERE `u`.`location_id` = 1
2010-02-28 15:52:14 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 16:01:18 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 16:01:18 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 16:01:18 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 16:01:19 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 16:01:19 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 16:01:19 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/jgrid.php[174] (jgrid) produce: Unknown or missing operator without gridName, assuming request to render grid
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Cache.php[86] (Cache_Core) __construct: Cache Library initialized
2010-02-28 16:01:19 -08:00 --- debug: modules/freeswitch/libraries/FreeSwitch.php[87] (FreeSwitch) getInstance: New instance of FreeSwitch telephony driver created.
2010-02-28 16:01:19 -08:00 --- debug: freepbx/libraries/Telephony.php[62] (Telephony) setDriver: Initialized telephony driver FreeSwitch
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.conditioning to call FreeSwitch_CallerId_Driver::conditioning
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.prenumber to call FreeSwitch_VoicemailSettings_Driver::prenumber
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/dialplan.php[35] (dialplan) register: Added hook for _telephony.postnumber to call FreeSwitch_VoicemailSettings_Driver::postnumber
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Input.php[67] (Input_Core) __construct: Disable magic_quotes_gpc! It is evil and deprecated: http://php.net/magic_quotes
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Input.php[145] (Input_Core) __construct: Global GET, POST and COOKIE data sanitized
2010-02-28 16:01:19 -08:00 --- debug: system/libraries/Session.php[90] (Session_Core) __construct: Session Library initialized
2010-02-28 16:01:19 -08:00 --- debug: modules/auth/libraries/Auth.php[75] (Auth_Core) __construct: Auth Library loaded
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.edit.view
2010-02-28 16:01:19 -08:00 --- debug: freepbx/helpers/plugins.php[70] (plugins) runEvents: Found and executing hook UserManager_Plugin->edit
2010-02-28 16:01:20 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column full_name doesnt exist in User
2010-02-28 16:01:20 -08:00 --- alert: freepbx/helpers/jgrid.php[343] (jgrid) add: Adding field to DQL failed (treating as non db source): Column account_type_name doesnt exist in User
2010-02-28 16:01:20 -08:00 --- debug: freepbx/helpers/jgrid.php[132] (jgrid) produce: Grid RESTful operator 'none'
2010-02-28 16:01:20 -08:00 --- debug: freepbx/helpers/jgrid.php[166] (jgrid) produce: Unknown or missing operator with gridName, assuming request for JSON
2010-02-28 16:01:20 -08:00 --- debug: freepbx/helpers/jgrid.php[853] (jgrid) _autoQuery: Adding: where(u.location_id =  1,~NULL~)
2010-02-28 16:01:20 -08:00 --- debug: freepbx/helpers/jgrid.php[759] (jgrid) getGridJson: JGRID QUERY IS: SELECT `u`.`user_id` AS `u__user_id`, `u`.`email_address` AS `u__email_address`, `u`.`last_login` AS `u__last_login`, `u`.`first_name` AS `u__first_name`, `u`.`last_name` AS `u__last_name`, `u`.`account_type` AS `u__account_type` FROM `user` `u` WHERE `u`.`location_id` = 1
2010-02-28 16:01:20 -08:00 --- debug: freepbx/helpers/plugins.php[62] (plugins) runEvents: Running event locationmanager.view
