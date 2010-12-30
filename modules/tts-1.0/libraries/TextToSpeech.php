<?php defined('SYSPATH') or die('No direct access allowed.');

class TextToSpeech
{
    public static function provideMediaWidget()
    {
        $view = new View('tts/widget');

        Media_Plugin::addComponent('Text to Speech', $view);
    }

    public static function generateConfiguration()
    {
        list($media, $xml, $base) = Event::$data;

        $media += array(
            'tts_text' => 'Thank you for calling, your call is important to us.',
            'tts_voice' => 'Flite/kal',
        );

        @list($engine, $speaker) = explode('/', $media['tts_voice']);

        $tts_string = 'say:' .preg_replace('/[^A-Za-z0-9.,!? ]/', '', $media['tts_text']);

        kohana::log('debug', 'Configuring an auto-attendant to use ' .$engine .' ' .$speaker .' to ' .$tts_string);

        $xml->setAttributeValue('', 'tts-engine', strtolower($engine));

        $xml->setAttributeValue('', 'tts-voice', strtolower($speaker));

        $xml->setAttributeValue('', 'greet-long', $tts_string);

        $xml->setAttributeValue('', 'greet-short', $tts_string);
    }
}