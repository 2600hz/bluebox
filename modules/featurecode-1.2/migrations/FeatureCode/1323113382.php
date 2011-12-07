<?php defined('SYSPATH') or die('No direct access allowed.');

class Migration_1323113382 extends Doctrine_Migration_Base
{
	public function preUp() {
	}

	public function up() {
		$this->createTable('custom_feature_code',array(
			'custom_feature_code_id'=>array('type'=>'integer', 'unsigned' => TRUE , 'notnull' => TRUE , 'primary' => TRUE, 'autoincrement' => TRUE),
		        'name'=>array('type'=>'string', 'notnull' => TRUE, 'minlength' => 2),
			'description'=>array('type'=>'string'),
			'dialplan_code'=>array('type'=>'string'),
			'registry'=>array('type'=>'array', 'default' => array()),
			'plugins'=>array('type'=>'array', 'default' => array()),
			'options'=>array('type'=>'array', 'default' => array()),
			'account_id'=>array('type'=>'integer','notnull'=>TRUE),
			'updated_at'=>array('type'=>'timestamp','notnull'=>TRUE),
			'created_at'=>array('type'=>'timestamp','notnull'=>TRUE)
		));
		$this->addColumn('feature_code','custom_feature_code_id','integer',11);
		$this->createForeignKey('feature_code','featurecode_customfeaturecode_fk',array('local' => 'custom_feature_code_id', 'foreign' => 'custom_feature_code_id', 'foreignTable'=>'custom_feature_code'));
	}

	public function postUp() {
		// This array contains:
		// 1. The original name, pre-featurecode-1.2
		// 2. The display name (used in drop-down)
		// 3. Description (not used, I think?)
		// 4. The dialplan.
		$featurecodes=array(
			array('ivr_return','IVR Return','','
<condition field="${ivr_path}" expression="(.*)-(.*)-.*+$" break="never">
	<action application="set" data="ivr_path=$1"/>
	<action application="transfer" data="$2"/>
	<anti-action application="set" data="ivr_path="/>
	<anti-action application="transfer" data="${vm-operator-extension}"/>
</condition>'),
			array('redial','Redial','Call the person you most recently called','<action application="transfer" data="${hash(select/${domain_name}-last_dial/${caller_id_number})}"/>'),
			array('call_return','Call Return','','<action application="transfer" data="${hash(select/${domain_name}-call_return/${caller_id_number})}"/>'),
			array('voicemail','Voicemail','Listen to voicemail for any extension (requires pin)','
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>'),
			array('voicemail_quickauth','Voicemail for calling extension','Listen to voicemail for your extension (requires pin)','
<condition field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}" expression="^(.+)@(.+)$">
	<action application="answer"/>
	<action application="sleep" data="1000"/>
	<action application="voicemail" data="check default $2 $1"/>
	<action application="hangup"/>
	<anti-action application="answer"/>
	<anti-action application="sleep" data="1000"/>
	<anti-action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>
	<anti-action application="hangup"/>
</condition>'),
			array('voicemail_noauth','Voicemail Preauthenticated','Listen to voicemail for your extension (does not require pin)','
<condition field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}" expression="^(.+)@(.+)$">
	<action application="answer"/>
	<action application="sleep" data="1000"/>
	<action application="set" data="voicemail_authorized=${sip_authorized}"/>
	<action application="voicemail" data="check default $2 $1"/>
	<action application="hangup"/>
	<anti-action application="answer"/>
	<anti-action application="sleep" data="1000"/>
	<anti-action application="voicemail" data="check default voicemail_%ACCOUNT_ID%"/>
	<anti-action application="hangup"/>
</condition>'),
			array('park','Park Call','','
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="valet_park" data="account_%ACCOUNT_ID% auto in 1 10"/>'),
			array('unpark','Unpark','','
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="valet_park" data="account_%ACCOUNT_ID% ask 1 10 10000 ivr/ivr-enter_ext_pound.wav"/>
<action application="hangup"/>'),
			array('echo','Echo test','Echos back anything said by caller immediately','
<action application="answer"/>
<action application="echo"/>'),
			array('delay_echo','Delayed Echo Test','Echos back anything said by caller, with 1 second delay','
<action application="answer"/>
<action application="delay_echo" data="1000"/>'),
			array('tone_test','Tone Test','Play a 2600hz Tone until the caller hangs up','
<action application="answer"/>
<action application="playback" data="tone_stream://%(1000,0,2600);loops=-1"/>'),
			array('hold_music','Hold Music','Play Music-on-hold until the caller hangs up','
<action application="answer"/>
<action application="playback" data="$${hold_music}"/>'),
			array('eavesdrop','Eavesdrop','Listen in on a currently running call','
<action application="answer"/>
<action application="eavesdrop" data="${hash(select/spymap/$1)}"/>'),
			array('uuid_standby','UUID Standby','','
<condition field="destination_number" expression="(.*)">
	<action application="set" data="res=${callcenter_config(agent set uuid agent_${agent_id} \'${uuid}\')}" />
	<action application="set" data="res=${callcenter_config(agent set type agent_${agent_id} \'uuid-standby\')}" />
	<action application="set" data="res=${callcenter_config(agent set status agent_${agent_id} \'Available (On Demand)\')}" />
	<action application="set" data="res=${callcenter_config(agent set state agent_${agent_id} \'Waiting\')}" />
	<action application="set" data="cc_warning_tone=tone_stream://%(200,0,500,600,700)"/>
	<action application="answer" />
	<action application="playback" data="$${hold_music}"/>
	<action application="transfer" data="$num"/>
</condition>'),
			array('agent_login','Agent Login','Mark caller as available for calls','
<action application="set" data="res=${callcenter_config(agent set status agent_${agent_id} \'Available\')}" />
<action application="answer" data=""/>
<action application="sleep" data="500"/>
<action application="playback" data="ivr/ivr-you_are_now_logged_in.wav"/>'),
			array('agent_logout','Agent Logout','Mark caller as unavailable for calls','
<action application="set" data="res=${callcenter_config(agent set status agent_${agent_id} \'Logged Out\')}" />
<action application="answer" data=""/>
<action application="sleep" data="500"/>
<action application="playback" data="ivr/ivr-you_are_now_logged_out.wav"/>')
		);
		foreach ($featurecodes AS &$fc) {
			$cfc=new CustomFeatureCode();
			$cfc['name']=$fc[1];
			$cfc['description']=$fc[2];
			$cfc['dialplan_code']=$fc[3];
			$cfc->save();
			$map[$fc[0]]=$cfc['custom_feature_code_id'];
		}
		foreach (Doctrine::getTable('FeatureCode')->findAll() AS $fc) {
			if (array_key_exists($fc['registry']['feature'],$map)) {
				$fc['custom_feature_code_id']=$map[$fc['registry']['feature']];
				$fc->save();
			}
		}
	}
}

