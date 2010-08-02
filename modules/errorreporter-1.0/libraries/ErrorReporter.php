<?php defined('SYSPATH') or die('No direct access allowed.');

class ErrorReporter
{
    public function provideHelpLink()
    {
        $session = Session::instance();

        $flashMessage = &Event::$data;

        $hash = 'message_' .time();

        $session->set($hash, $flashMessage);

        $flashMessage .= html::anchor('errorreporter/inform/' .$hash, 'Help!', array('class' => 'support_help qtipAjaxForm', 'style' => 'float:right;'));
    }
}
