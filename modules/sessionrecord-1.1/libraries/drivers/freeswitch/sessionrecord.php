<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SessionRecord_Driver extends FreeSwitch_Base_Driver
{

    public static function set($base)
    {
    }

    public static function delete($base)
    {
    }

    public static function postroute()
    {

        $xml = FreeSWITCH::createExtension('sessionrecord');

        $condition = '/condition';

        $xml->update($condition . '/action[@application="set"][@data="media_bug_answer_req=true"]');

        $xml->update($condition .'/action[@application="set"][@data="RECORD_TITLE=Recording ${destination_number} ${caller_id_number} ${strftime(%Y-%m-%d %H:%M)}"]');
        $xml->update($condition .'/action[@application="record_session"][@data="$${base_dir}\/recordings\/${uuid}.wav"]');

    }

}
