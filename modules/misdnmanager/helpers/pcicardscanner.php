<?php


/**
 * pcicardscanner.php - card scanner helper
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package FreePBX3
 * @subpackage MisdnManager
 *
 */
class pcicardscanner
{
    private static $instance;
    private static $cards;


    /**
     *
     * @return CardScanner
     */
    public static function getInstance()
    {
        if (!self::$instance)
        {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }


    public static function scan()
    {
        $command = 'lspci -nv';

        exec($command, $result, $return);

        $cards = array();

        //        for($i = count($result)-1; $i >= 0 ; $i--)
        for($i = 0; $i < count($result); $i++)
        {
            if(preg_match('/[0-9a-fA-F]{2}\:[0-9a-fA-F]{2}\.[0-9a-fA-F]{1}$/', substr($result[$i], 0, 7)))
            {
                $addr = strtolower(substr($result[$i], 0, 7));
                $i++;
                if(stripos($result[$i], "subsystem") === false)
                {
                    continue;
                }
                // extract the subsystem id
                $subsysID = substr($result[$i], strlen($result[$i])-9, 9);
                
                // check if this subsystem belongs to a supported card
                $model = MisdnManager::getModel($subsysID);
                if (!$model)
                {
                    // not supported, skip this one
                    continue;
                }
                
                self::$cards[] = array('addr' => $addr, 'subsys' => $subsysID, 'vendor' => $model->MisdnCardVendor->vendor, 'model' => $model->model);
            }
        }

    }


    public static function toArray()
    {
        return self::$cards;
    }


    public static function toJSON()
    {
        return json_encode(self::$cards);
    }
}