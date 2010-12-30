<?php defined('SYSPATH') or die('No direct access allowed.');

class TTS_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'tts';
    public static $displayName = 'Text to Speech';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Allows the creation of text to speech media in the system';
    public static $description = 'This module handles exposing text to speech options as media resources to other modules such as AutoAttendant, ect.';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Media/';
    public static $navURL = '/tts/index';
    public static $navSubmenu = array(
        'List Engines' => '/tts/index',
        'Add TTS Engine' => '/tts/create',
        'Edit TTS Engine' => array(
            'url' => '/tts/edit',
            'disabled' => TRUE
        ) ,
        'Delete TTS Engine' => array(
            'url' => '/tts/delete',
            'disabled' => TRUE
        )
    );

    public function postInstall()
    {
        $tts_engine = new TTSEngine();

        $tts_engine['name'] = 'Flite';

        $tts_engine['description'] = 'Flite (festival-lite) is an opensource test to speech engine developed at CMU';

        $tts_engine['speakers'] = array('kal', 'slt', 'rms', 'awb');

        $tts_engine->save();
    }
}