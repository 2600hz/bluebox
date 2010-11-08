<?php defined('SYSPATH') or die('No direct access allowed.');

class TextToSpeech
{
    public static function provideMediaWidget()
    {
        $view = new View('tts/widget');

        media::addComponent('Text to Speech', $view);
    }
}