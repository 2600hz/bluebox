<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * class DeviceScanner
 *
 * Add the following with visudo
 * Cmnd_Alias FREESWITCH_CMDS = /usr/bin/nmap
 * www-data ALL=NOPASSWD: FREESWITCH_CMDS
 *
 * @todo implement cache
 */
class DeviceScanner
{
    protected static $scan;
    private static $instance;
    private static $cidr;
    private static $deviceMap;
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }
    public static function toArray()
    {
        return self::$deviceMap;
    }
    public static function toJSON()
    {
        return json_encode(self::$deviceMap);
    }
    public static function setCIDR($cidr)
    {
        $num = "([0-9]|1?\d\d|2[0-4]\d|25[0-5])";
        $range = "([1-9]|1\d|2\d|3[0-2])";
        if (preg_match("/^$num\.$num\.$num\.$num(\/$range)?$/", $cidr)) {
            self::$cidr = $cidr;
            return self::$instance;
        }
        throw new Exception('Invalid CIDR. See http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing');
    }
    public static function scanDevices()
    {
        $command = "sudo nmap -sU -sS -p 5060,4569 -oX - " . self::$cidr;
        exec($command, $result, $return);
        $result = implode("\n", $result);
        if (!$result) throw new Exception("Failed to run nmap, return with status $return");
        //$result = file_get_contents("phones.xml");
        $xml = new DOMDocument();
        $xml->loadXML($result);
        $hosts = $xml->getElementsByTagName('host');
        foreach($hosts as $row) {
            $result = self::dom2array_full($row);
            $status = $result['status']['state'];
            foreach($result['address'] as $address) {
                switch ($address['addrtype']) {
                case 'mac':
                    $mac = $address['addr']; //mac
                    if (isset($address['vendor'])) {
                        $vendor = $address['vendor'];
                    } else {
                        $vendor = "Failed to find vendor";
                    }
                    break;

                case 'ipv4':
                    $ip = $address['addr']; //ip
                    break;
                }
            }
            $supported = EndpointManager::getVendor($mac) ? true : false;
            self::$deviceMap[] = array(
                'status' => $status,
                'mac' => $mac,
                'ip' => $ip,
                'vendor' => $vendor,
                'supported' => $supported
            );
        }
        return self::$instance;
    }
    public static function dom2array_full($node)
    {
        $result = array();
        if ($node->nodeType == XML_TEXT_NODE) {
            $result = $node->nodeValue;
        } else {
            if ($node->hasAttributes()) {
                $attributes = $node->attributes;
                if (!is_null($attributes)) foreach($attributes as $index => $attr) $result[$attr->name] = $attr->value;
            }
            if ($node->hasChildNodes()) {
                $children = $node->childNodes;
                for ($i = 0; $i < $children->length; $i++) {
                    $child = $children->item($i);
                    if ($child->nodeName != '#text') if (!isset($result[$child->nodeName])) $result[$child->nodeName] = self::dom2array_full($child);
                    else {
                        $aux = $result[$child->nodeName];
                        $result[$child->nodeName] = array(
                            $aux
                        );
                        $result[$child->nodeName][] = self::dom2array_full($child);
                    }
                }
            }
        }
        return $result;
    }
}
