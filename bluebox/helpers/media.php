<?php defined('SYSPATH') or die('No direct access allowed.');

class media
{
    public static function generateConfig(&$base, &$xml = NULL)
    {
        if (empty($base['plugins']['media']['type']))
        {
            kohana::log('error', 'Attempted to configure media without a type');

            return;
        }

        $type = $base['plugins']['media']['type'];

        $data = array(
            $base['plugins']['media'],
            $xml,
            $base
        );

        kohana::log('debug', 'Attempting to configure media type ' .$type);

        Event::run('bluebox.media.' .$type, $data);
    }
}