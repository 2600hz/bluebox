<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Message
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class message
{
    private static $logLevels = array('error', 'alert', 'info', 'success');

    /**
     * This function sets a generic message with options
     *
     * @param string the message to display
     * @param array a list of options to apply to the message
     * @return void
     */
    public static function set($text = '', $options = array())
    {
        if (!is_array($options))
        {
            $options = array('type' => $options);
        }

        // populate the defualt options
        $options += array (
            'key' => 'default',
            'type' => 'error',
            'translate' => TRUE,
            'redirect' => FALSE
        );

        // if this is an unknown type then invalid
        if (!in_array($options['type'], self::$logLevels))
        {
            kohana::log('error', 'Invalid message type ' . $options['type']);
            
            return FALSE;
        }

        // if there is no message then invalid
        if (empty($text))
        {
            kohana::log('error', 'A message must be supplied.');
            
            return FALSE;
        }

        // log a success message as debug
        if ($options['type'] == 'success')
        {
            kohana::log('debug', $options['key'] .' - ' . $text);
        } 
        else
        {
            kohana::log($options['type'], $options['key'] .' - ' . $text);
        }

        // translate the message unless we have been asked not to
        if (empty($options['translate']))
        {
            $options['text'] = $text;
        } 
        else
        {
            $options['text'] = __($text);
        }

        // append this message into any existing session bluebox_messages
        $currentMessages = Session::instance()->get('bluebox_message', array());

        $currentMessages[] = $options;

        Session::instance()->set('bluebox_message', $currentMessages);

        // If given a redirect URL then go to it
        if (!empty($options['redirect']) && !Request::is_ajax())
        {
            url::redirect($options['redirect']);
        }
        
        return TRUE;
    }

    public static function error($text, $options = array())
    {
        $options += array('type' => 'error');

        self::set($text, $options);
    }

    public static function warning($text, $options = array())
    {
        $options += array('type' => 'warning');

        self::set($text, $options);
    }

    public static function alert($text, $options = array())
    {
        $options += array('type' => 'alert');

        self::set($text, $options);
    }

    public static function success($text, $options = array())
    {
        $options += array('type' => 'success');

        self::set($text, $options);
    }


    /**
     * Renders set session flash messages
     *
     * @param array an array of message types to render
     * @param array an array of rendering options, see function for details
     * @return array
     */
    public static function render($displayOnlyType = array(), $options = array())
    {
        // build up the default options
        /**
         * growl            If TRUE all errors are generate a growl message, this can be
         *                  disabled with FLASE or it can be an array of message types
         * growlTemplate    This is the growl JS statement, any arbitrary var used in
         *                  the second parameter of setMessage can be referenced via {foo}
         *                  defaults avaliable are {key}, {type}, and {text}
         * html             If TRUE all errors are generate a html message, this can be
         *                  disabled with FLASE or it can be an array of message types
         * htmlTemplate     This is the html markup, any arbitrary var used in
         *                  the second parameter of setMessage can be referenced via {foo}
         *                  defaults avaliable are {key}, {type}, and {text}
         * inline           If inline is true then this puts the messages outputs into
         *                  the buffer
         */
        $options += array (
            'growl' => array('alert', 'info', 'success'),
            'growlTemplate' => '$.jGrowl(\'{text}\', { theme: \'{type}\', life: 5000 });',
            'html' => array('error'),
            'htmlTemplate' => '<div class="{type}">{text}</div>',
            'inline' => TRUE
        );

        // get the messages
        $flashMessages = Session::instance()->get_once('bluebox_message', array());

        // set up some empty result arrays
        $growl = array();

        $html = array();

        // loop through each message
        foreach ($flashMessages as $flashKey => $flashMessage)
        {
            // if we have been asked to show a type and this isnt it then move on
            if (!empty($displayOnlyType) && $displayOnlyType != $flashMessage['type'])
            {
                continue;
            }

            // allow the templates in options to be populated with anything in the message subarray
            $search = array_keys($flashMessage);

            $search = array_map(create_function('$v', 'return \'{\' . $v . \'}\';'), $search);

            // if we are generating a growl message then do so
            if (!empty($options['growl']))
            {
                if ( $options['growl']=== TRUE || (is_array($options['growl']) && in_array($flashMessage['type'], $options['growl'])))
                {
                    Event::run('bluebox.message_growl', $flashMessage['text']);

                    $flashMessage['text'] = str_replace('\'', '\\\'', $flashMessage['text']);

                    $growl[] = str_replace($search, $flashMessage, $options['growlTemplate']);
                }
            } 

            // if we are generating a html markup then do so
            if (!empty($options['html']))
            {
                if ( $options['html']=== TRUE || (is_array($options['html']) && in_array($flashMessage['type'], $options['html'])))
                {
                    Event::run('bluebox.message_html', $flashMessage['text']);

                    $flashMessage['text'] = str_replace('"' , '\'', $flashMessage['text']);

                    $html[] = str_replace($search, $flashMessage, $options['htmlTemplate']);
                }
            }

            // this message is assumed to have been displayed, remove it
            unset($flashMessages[$flashKey]);
        }

        // save back any messages that did not get displayed to the user
        Session::instance()->set('bluebox_message', $flashMessages);

        // if we are doing this inline then echo it out here and now
        if (!empty($options['inline']))
        {
            if (!empty($html))
            {
                echo implode(' ', $html);
            }

            if (!empty($growl)) 
            {
                jquery::addPlugin('growl');
                
                jquery::evalScript(implode(' ', $growl));
            }
        }

        // if the user wants the results then give it to them
        return compact('growl', 'html');
    }

    public static function renderHelp()
    {
        return html::anchor('support/request_help', 'Help!', array('class' => 'support_help qtipAjaxForm', 'style' => 'float:right;'));
    }
}
