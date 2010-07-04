<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Contextmanager_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj) {}
    public static function delete($obj) {}

    public function conditioning() {
        $xml = FreeSWITCH::createExtension('unloop');
        $condition = '/condition[@field="${sip_looped_call}"][@expression="^true$"]';
        $xml->update($condition .'/action[@application="deflect"][@data="${destination_number}"]');
    }

    public function prenumber() {
        $xml = Telephony::getDriver()->xml;

        $obj = Event::$data;
        if (!$obj) return TRUE;
        
        if (($obj instanceof DeviceNumber) AND !empty($obj->NumberContext[0]->Context->options['prepend_in'])) {
            kohana::log('debug', 'Adding inbound BLeg prepend to ' .$obj->number);
            //$xml->update('/action[@application="export"][@bluebox="settingPrependInApp"][@data="nolocal:execute_on_answer=playback \/usr\/local\/freeswitch\/sounds\/en\/us\/callie\/voicemail\/8000\/vm-hello.wav"]');
            $xml->update('/action[@application="export"][@bluebox="settingPrependInApp"][@data="nolocal:execute_on_answer=playback \/usr\/local\/freeswitch\/sounds\/en\/us\/callie\/voicemail\/8000\/vm-hello.wav"]');
            //$xml->update('/action[@application="set"][@bluebox="settingPrependInApp"][@data="bridge_post_execute_bleg_app=playback"]');
            //$xml->update('/action[@application="set"][@bluebox="settingPrependInData"][@data="bridge_post_execute_bleg_data=\/usr\/local\/freeswitch\/sounds\/en\/us\/callie\/voicemail\/8000\/vm-hello.wav"]');
        }
    }

    public function postExecute() {
        Kohana::log('debug', 'Adding catch all to ' .FreeSwitch::getCurrentContext());

        // See if we can can find our context so we can determine its settings
        $contextID = str_replace('context_', '', FreeSwitch::getCurrentContext());
        $context = Doctrine::getTable('Context')->find($contextID);

        if ($context) {
            $xml = FreeSWITCH::createExtension('catch_all');

            $condition = '/condition[@bluebox="outbound_setup"]';
            $xml->deleteNode($condition);
            $xml->update($condition .'/action[@application="set"][@bluebox="settingEndBridge"][@data="hangup_after_bridge=true"]');
            $xml->update($condition .'/action[@application="set"][@bluebox="settingFail"][@data="continue_on_fail=NORMAL_TEMPORARY_FAILURE,TIMEOUT,NO_ROUTE_DESTINATION"]');

            if (!empty($context->options['disable_outbound_bypass'])) {
                $xml->update($condition .'/action[@application="set"][@bluebox="settingBypass"][@data="bypass_media=false"]');
            } else {
                //$xml->update($condition .'/action[@application="set"][@bluebox="settingBypass"][@data="bypass_media=true"]');
                $xml->update($condition .'/action[@application="set"][@bluebox="settingBypass"][@data="bypass_media=false"]');
            }

            // Set up the outbound caller id, defaulting to the phones settings when present
            $condition = '/condition[@bluebox="callerid_conditioning"][@field="${outbound_caller_id_number}"][@expression="^$"][@break="never"]';
            $xml->deleteNode($condition);
            if (empty($context->options['default_cid']) || strlen($context->options['default_cid']) != 10) {
                kohana::log('error', 'No default CID for ' .$context->name);
                $xml->update($condition .'/action[@application="set"][@data="effective_caller_id_number=555555' .str_pad($contextID, 4, '0', STR_PAD_LEFT) .'"]');
            } else {
                $xml->update($condition .'/action[@application="set"][@data="effective_caller_id_number=' .$context->options['default_cid'] .'"]');
            }
            $xml->update($condition .'/anti-action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

            // Check if the outbound call is 911
            $condition = '/condition[@bluebox="emergency"][@field="destination_number"][@expression="^911$"][@break="on-true"]';
            $xml->deleteNode($condition);
            $xml->update($condition .'/action[@application="lcr"][@data="$1"]');
            $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
            $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
            $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
            $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again or use a different phone."]');
            $xml->update($condition .'/action[@application="hangup"]');

            // Do we need to prepend outbound calls?
            $condition = '/condition[@bluebox="prepend_outbound"][@break="never"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['prepend_out'])) {
                kohana::log('debug', 'Adding outbound prepend to ' .$context->name);
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call will continue after this message, however your account requires attention.  Please contact your sales representative as soon as possible to avoid service disruption."]');
                $xml->update($condition .'/action[@application="set"][@bluebox="settingBypass"][@data="bypass_media=false"]');
            }

            // disable all outbound calls?
            if (empty($context->options['outbound'])) {
                // This will effectively disable all outbound rules
                $context->options = array();
            }

            // Check if the outbound call is 411
            $condition = '/condition[@bluebox="directory"][@field="destination_number"][@expression="^411$"][@break="on-true"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['directory']) && $context->options['directory'] == 1) {
                $xml->update($condition .'/action[@application="lcr"][@data="18004664411"]');
                $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again later."]');
                $xml->update($condition .'/action[@application="hangup"]');
            }

            // Check if the outbound call is international
            $condition = '/condition[@bluebox="international"][@field="destination_number"][@expression="^011[0-9]{4,}$"][@break="on-true"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['international']) && $context->options['international'] == 1) {
                $xml->update($condition .'/action[@application="lcr"][@data="$1"]');
                $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again later."]');
                $xml->update($condition .'/action[@application="hangup"]');
            }

            // Check if the outbound call is tollfree - 844, 833, 822, 881, 882?
            $condition = '/condition[@bluebox="tollfree"][@field="destination_number"][@expression="^1?(8[05-8]{2}[2-9][0-9]{6})$"][@break="on-true"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['tollfree']) && $context->options['tollfree'] == 1) {
                $xml->update($condition .'/action[@application="lcr"][@data="1$1"]');
                $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again later."]');
                $xml->update($condition .'/action[@application="hangup"]');
            }

            // Check if the outbound call is toll
            $condition = '/condition[@bluebox="toll"][@field="destination_number"][@expression="^1?(900[2-9][0-9]{6})$"][@break="on-true"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['toll']) && $context->options['toll'] == 1) {
                $xml->update($condition .'/action[@application="lcr"][@data="1$1"]');
                $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again later."]');
                $xml->update($condition .'/action[@application="hangup"]');
            }
            
            // Check if the outbound call is domestic
            $condition = '/condition[@bluebox="domestic"][@field="destination_number"][@expression="^1?([2-9][0-8][0-9][2-9][0-9]{6})$"][@break="on-true"]';
            $xml->deleteNode($condition);
            if (!empty($context->options['domestic']) && $context->options['domestic'] == 1) {
                $xml->update($condition .'/action[@application="lcr"][@data="1$1"]');
                $xml->update($condition .'/action[@application="bridge"][@data="${lcr_auto_route}"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
                $xml->update($condition .'/action[@application="speak"][@data="Your call can not be connected at this time, please try again later."]');
                $xml->update($condition .'/action[@application="hangup"]');
            }

            // Catch all and display an error, this is at the bottom of the context so if we got here there must not have been any match
            $condition = '/condition[@bluebox="nomatch_action"]';
            $xml->deleteNode($condition);
            $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');
            $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');
            $xml->update($condition .'/action[@application="speak"][@data="The number you have dialed is invalid, please check the number and try again."]');
            $xml->update($condition .'/action[@application="hangup"]');
        }
    }
}