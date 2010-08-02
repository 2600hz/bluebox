<?php defined('SYSPATH') or die('No direct access allowed.');

class astrsk
{
    public static function getNumberOptions($number)
    {
        $numberOptions = array();

        if (!empty($number['registry']))
        {
            $numberOptions = $number['registry'];
        }

        $numberOptions += array(
            'ignoreFWD' => FALSE,
            'ringtype' => '${us-ring}',
            'timeout' => 30
        );

        switch ($numberOptions['ringtype'])
        {
            case 'moh':
                $numberOptions['ringtype'] = 'local_stream:\/\/moh';

                break;

            case 'us-ring':
            default:
                $numberOptions['ringtype'] = '${us-ring}';
        }

        return $numberOptions;
    }

    public static function getTransferToNumber($number_id)
    {
        $numberParts = explode('_', $number_id);

        if (count($numberParts) > 1)
        {
            $number_id = $numberParts[0];

            $context = 'context_' .$numberParts[1];
        }
        else
        {
            $context = self::getContextOfNumber($number_id);
        }

        $n = Doctrine::getTable('Number')->find($number_id);

        if(!$n)
        {
            return false;
        }
        
        return sprintf("%s, %s, 1", $context, $n['number']);
    }

    public static function getContextOfNumber($number_id)
    {
        $nv = Doctrine::getTable('NumberContext')->findOneByNumberId($number_id);

        if(!$nv)
        {
            return false;
        }

        return 'context_' .$nv['Context']['context_id'];
    }
}