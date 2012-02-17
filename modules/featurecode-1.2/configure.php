<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCode_1_2_0_Configure extends Bluebox_Configure
{
    public static $version = '1.2.0';
    public static $packageName = 'featurecode';
    public static $displayName = 'Feature Codes';
    public static $author = 'Darren Schreiber &amp; Jort Bloem';
    public static $vendor = 'Bluebox &amp; BTG';
    public static $license = 'MPL';
    public static $summary = 'FreeSWTICH feature code module';
    public static $description = 'Allows configuration of feature codes and stock FreeSWITCH features';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'freeswitch' => 0.1,
        'core' => 0.1
    );

    public static $navStructures = array(
	array(
		'navLabel' => 'Feature Codes',
		'navBranch' => '/Applications/',
		'navURL' => 'featurecode/index',
		'navSubmenu' => array(
			'Search Feature Codes' => '/featurecode/index',
			'Add Feature Codes' => '/featurecode/create',
		),
	),
	array(
		'navLabel' => 'Custom Feature Codes',
		'navBranch' => '/System/',
		'navURL' => 'customfeaturecode/index',
		'navSubmenu' => array(
			'Search Custom Feature Codes' => '/customfeaturecode/index',
			'Add Custom Feature Codes' => '/customfeaturecode/create',
		)
	)
   );
   public function postInstall() {
	parent::postInstall();
	$featurecodes=array(
		array('IVR Return','','<condition field="${ivr_path}" expression="(.*)-(.*)-.*+$" break="never">\n\t<action application="set" data="ivr_path=$1"/>\n\t<action application="transfer" data="$2"/>\n\t<anti-action application="set" data="ivr_path="/>\t<anti-action application="transfer" data="${vm-operator-extension}"/>\n</condition>'),
		array('Redial','Call the person you most recently called','<action application="transfer" data="\${hash(select/\${domain_name}-last_dial/\${caller_id_number})}"/>'),
		array('Call Return','','<action application="transfer" data="\${hash(select/\${domain_name}-call_return/\${caller_id_number})}"/>'),
		array('Voicemail','Listen to voicemail for any extension (requires pin)','<action application="answer"/>\n<action application="sleep" data="1000"/>\n<action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>'),
		array('Voicemail Quickauth','Listen to voicemail for your extension (requires pin)','<condition field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}" expression="^(.+)@(.+)$">\n\t<action application="answer"/>\n\t<action application="sleep" data="1000"/>\n\t<action application="voicemail" data="check default \$2 \$1"/>\n\t<action application="hangup"/>\n\t<anti-action application="answer"/>\n\t<anti-action application="sleep" data="1000"/>\n\t<anti-action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>\n\t<anti-action application="hangup"/>\n</condition>'),
		array('Voicemail NoAuth','Listen to voicemail for your extension (does not require pin)','<condition field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}" expression="^(.+)@(.+)$">\n\t<action application="answer"/>\n\t<action application="sleep" data="1000"/>\n\t<action application="set" data="voicemail_authorized=\${sip_authorized}"/>\n\t<action application="voicemail" data="check default \$2 \$1"/>\n\t<action application="hangup"/>\n\t<anti-action application="answer"/>\n\t<anti-action application="sleep" data="1000"/>\n\t<anti-action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>\n\t<anti-action application="hangup"/>\n</condition>'),
		array('Park Call','','<action application="answer"/>\n<action application="sleep" data="1000"/>\n<action application="valet_park" data="account_%ACCOUNT_ID% auto in 1 10"/>'),
		array('Unpark','','<action application="answer"/>\n<action application="sleep" data="1000"/>\n<action application="valet_park" data="account_%ACCOUNT_ID% ask 1 10 10000 ivr/ivr-enter_ext_pound.wav"/>\n<action application="hangup"/>'),
		array('Echo test','','<action application="answer"/>\n<action application="echo"/>'),
		array('Delayed Echo Test','','<action application="answer"/>\n<action application="delay_echo" data="1000"/>'),
		array('Tone Test','Play a 2600hz Tone until the caller hangs up','<action application="answer"/>\n<action application="playback" data="tone_stream://%(1000,0,2600);loops=-1"/>'),
		array('Hold Music','Play Music-on-hold until the caller hangs up','<action application="answer"/>\n<action application="playback" data="\$\${hold_music}"/>')
	);
	foreach ($featurecodes AS $fc) {
		$cfc=new CustomFeatureCode();
		$cfc['name']=$fc[0];
		$cfc['description']=$fc[1];
		$cfc['dialplan_code']=$fc[2];
		$cfc->save();
	}
   }
}
