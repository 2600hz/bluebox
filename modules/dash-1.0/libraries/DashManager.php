<?php
class DashManager {
    public static function addCSS () {
        stylesheet::add('dash',20);
    }

    public static function renderActions()
    {
        jquery::addPlugin('dialog');
        jquery::addPlugin('form');
        jquery::addPlugin('growl');
        jquery::addPlugin('autocomplete');

        return $view = new View('dash/actions');
    }

    public static function renderDialogs()
    {
        return $view = new View('dash/dialogs');
    }

    public static function addContact($name, $number, $type = 'home')
    {
        $contact = new Contact();
        $contact->first_name = $name;
        switch($type)
        {
            case 'home':
                $contact->home_number;
                break;

            case 'work':
                $contact->work_number;
                break;

            case 'mobile':
                $contact->mobile_number;
                break;

            case 'pager':
                $contact->pager_number;
                break;

            case 'fax':
                $contact->fax_number;
                break;

            default:
                break;
        }
        $contact->user_id = $_SESSION['user_id'];
        $contact->save();
    }


    public function getMessages($type)
    {
        /*
        set_time_limit(0);
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        $messageQueue = array();
        $messageQueueSize = 0;

        SELECT * From dash_message WHERE user_id = %d

        foreach($result as $message)
                if($results

        $message[pk_id] = array('header', 'message', 'type');
        if(type == 'flash')
                delete
        }
        */
    }

 } 
