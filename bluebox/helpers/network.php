<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Network
 * @author     K Anderson <bitbashing@gmail.com>
 * @author     Michael Phillips
 * @license    Mozilla Public License (MPL)
 */
class network
{
    public static function classify($ip = NULL, $var = NULL)
    {
        $accessibility = $broadcast = $type = 'unknown';

        $error = FALSE;

        $ips = explode('.', $ip);

        if (count($ips) != 4)
        {
            $error = 'Invalid IP. Must be provided in dot-decimal notation.';
        }
        else
        {
            $ips = array_map('trim', $ips);

            list($a, $b, $c, $d) = $ips;

            if ($a == '127')
            {
                $type = 'localhost';

                $accessibility = 'loopback';
            }
            else if ($a == '169' && $b == '254')
            {
                $type = 'RFC3330 link-local';

                $accessibility = 'loopback';
            }
            else if ($a == '10')
            {
                $type = 'RFC1918 24 bit block';

                $accessibility = 'private';
            }
            else if ($a == '172' && ( $b >= 16 && $b <= 31 ))
            {
                $type = 'RFC1918 20 bit block';

                $accessibility = 'private';
            }
            else if ($a == '192' && $b == '168')
            {
                $type = 'RFC1918 16 bit block';

                $accessibility = 'private';
            } 
            else
            {
                $type = 'IPv4 public address';
                
                $accessibility = 'public';
            }

            // ya I know this isnt accurate for many reasons, but we
            // dont have the netmask, or need to be that exact.....
            if ($a == '255' || $b == '255' || $c == '255' || $d == '255')
            {
                $broadcast = TRUE;
            } 
            else
            {
                $broadcast = FALSE;
            }
        }

        if (!is_null($var) && isset($$var))
        {
            if ($error)
            {
                throw new Exception ($error);
            }
            
            return $$var;
        } 
        else
        {
            return compact('accessibility', 'broadcast', 'type', 'error');
        }
    }

    public static function cidr2range($cidr)
    {
        $cidr = explode('/', $cidr);

        if (count($cidr) != 2)
        {
            return array('start' => '', 'end' => '');
        }

        list($base, $bits) = $cidr;

        $base = explode('.', $base);

        if (count($base) != 4)
        {
            return array('start' => '', 'end' => '');
        }
        
        list($a, $b, $c, $d) = $base;

        $i = ($a << 24) + ($b << 16) + ($c << 8) + $d;

        $mask = $bits == 0 ? 0 : (~0 << (32 - $bits));

        $low = $i & $mask;

        $high = $i | (~$mask & 0xFFFFFFFF);

        return array('start' => long2ip($low), 'end' => long2ip($high));
    }

    public static function inNetwork($ip, $cidr)
    {
        extract(self::cidr2range($cidr));

        $ip = explode('.', $ip);

        if (count($ip) != 4)
        {
            return FALSE;
        }
        
        list($a, $b, $c, $d) = $ip;

        $check = ($a << 24) + ($b << 16) + ($c << 8) + $d;

        if ($check >= $low && $check <= $high)
        {
            return TRUE;
        }
        
        return FALSE;
    }

    public static function mask2cidr($mask)
    {
        $long = ip2long($mask);

        $base = ip2long('255.255.255.255');

        return 32-log(($long ^ $base)+1,2);
    }

    public static function guessServerIP()
    {
        $possibleIps  = array();

        $possibleIps[] = self::getInterfaceIp('eth0');

        $possibleIps[] = self::getInterfaceIp('eth1');

        $possibleIps[] = self::getInterfaceIp('eth2');

        if (isset($_SERVER['LOCAL_ADDR']))
        {
            $possibleIps[] = $_SERVER['LOCAL_ADDR'];
        }
        
        if (isset($_SERVER['SERVER_ADDR']))
        {
            $possibleIps[] = $_SERVER['SERVER_ADDR'];
        }
        
        if (isset($_SERVER['SERVER_NAME']))
        {
            $possibleIps[] = gethostbyname($_SERVER['SERVER_NAME']);
        }

        if (isset($_SERVER['HTTP_HOST']))
        {
            $possibleIps[] = gethostbyname($_SERVER['HTTP_HOST']);
        }
        
        try
        {
            $possibleIps[] = Kohana::config('freeswitch.ESLHost', FALSE);
        } 
        catch (Exception $e)
        {
            
        }

        try
        {
            $possibleIps[] = Kohana::config('asterisk.AmiHost', FALSE);
        }
        catch (Exception $e)
        {

        }

        $possibleIps = array_unique(array_filter($possibleIps));

        $ipAddress = '127.0.0.1';
        
        foreach ($possibleIps as $possibleIp)
        {
            try
            {
                $accessibility = self::classify($possibleIp, 'accessibility');
            } 
            catch (Exception $e)
            {
                continue;
            }

            if ($accessibility == 'public')
            {
                $ipAddress = $possibleIp;

                break;
            } 
            else if ($accessibility == 'private')
            {
                $ipAddress = $possibleIp;
            }
        }

        // sometimes lighttpd adds ::ffff: to the beinging of the ip, but FS doesn't like that.
        $ipTmp = str_replace('::ffff:', '', $ipAddress);

        if(filter_var($ipTmp, FILTER_VALIDATE_IP))
        {
                $ipAddress = $ipTmp;
        }
        
        return $ipAddress;
    }

    public static function status()
    {
        $status['interface']['eth0'] = self::getInterfaceIp('eth0');

        $status['interface']['eth1'] = self::getInterfaceIp('eth1');

        $status['interface']['eth2'] = self::getInterfaceIp('eth2');
        
        return $status;
    }

    public static function getInterfaceIP($iface = 'eth0')
    {
        $ip = $_SERVER['SERVER_ADDR'];

        if($ip != '127.0.0.1')
        {
            return $ip;
        }

        $ip =  exec("/sbin/ifconfig $iface | grep 'inet addr:' | cut -d: -f2 | awk '{ print \$1}'");

        if(!filter_var($ip, FILTER_VALIDATE_IP))
        {
            return FALSE;
        }

        return $ip;
    }

    public static function testUDP($host = '127.0.0.1', $port = 0)
    {
        $fp = fsockopen("udp://$host", $port, $errno, $errstr);

        if (!$fp)
        {
            // log "ERROR: $errno - $errstr<br />\n";
            return FALSE;
        }

        fclose($fp);

        return TRUE;
    }

    public static function testTCP($host = '127.0.0.1', $port = 0)
    {
        $fp = fsockopen("tcp://$host", $port, $errno, $errstr);

        if (!$fp)
        {
            // log "ERROR: $errno - $errstr<br />\n";
            return FALSE;
        }

        fclose($fp);

        return TRUE;
    }

    public static function getPublicIP()
    {
        $result = file_get_contents('http://sip.mphill.com/ip.php');

        if($result)
        {
            $ip = json_decode($result, TRUE);
            return $ip['ip_addr'];
        }
        return FALSE;
    }
}