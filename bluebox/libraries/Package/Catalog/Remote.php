<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Catalog_Remote
{
    public static function queryRepositories($repos = NULL)
    {
        if (is_null($repos))
        {
            $repos = Kohana::config('core.repositories');
        }

        if (!is_array($repos))
        {
            $repos = array($repos);
        }

        if (empty($repos))
        {
            return array();
        }

        $remoteCatalogs = array();

        foreach ($repos as $repo)
        {
            if(!$repoXMLCatalog = self::fetch($repo))
            {
                continue;
            }

            $remoteCatalog = self::fromXML($repoXMLCatalog);

            foreach ($remoteCatalog as $package)
            {
                Package_Catalog_Standardize::packageData($package, NULL);

                Package_Catalog_Standardize::navigation($package);

                if (empty($package['identifier']))
                {
                    Package_Message::log('alert', 'Remote repo ' .$repo .' provided an invalid package, ignoring!');

                    continue;
                }

                if (array_key_exists($package['identifier'], Package_Catalog::getCatalog()))
                {
                    Package_Message::log('debug', 'Remote repo ' .$repo .' provided existing package ' .$package['packageName'] .' version ' .$package['version'] .', ignoring');

                    continue;
                }

                Package_Message::log('debug', 'Remote repo ' .$repo .' provided new package ' .$package['packageName'] .' version ' .$package['version']);
                
                $package['status'] = Package_Manager::STATUS_UNINSTALLED;

                $remoteCatalogs[$package['identifier']] = $package;
            }
        }

        return $remoteCatalogs;
    }

    public function createRepo()
    {
        Package_Catalog::disableRemote();

        $repoCatalog = Package_Catalog::getCatalog();

        foreach($repoCatalog as $key => &$package)
        {
            unset($package['configure_class']);
            unset($package['directory']);
            unset($package['upgrades']);
            unset($package['downgrades']);
            unset($package['datastore_id']);
            unset($package['status']);
        }

        header('Content-type: text/xml');
        
        echo self::toXml($repoCatalog);

        flush();
        die();
    }

    protected static function fetch($URL)
    {
        // needs caching...
        if (!$repoXMLCatalog = @file_get_contents($URL))
        {
            Package_Message::log('alert', 'Unable to get catalog data from ' .$URL);

            return FALSE;
        }

        Package_Message::log('debug', 'Retrieved catalog data from ' .$URL);

        return $repoXMLCatalog;
    }

    protected static function toXml($data, $rootNodeName = 'packages', $xml = null)
    {
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if (is_null($xml))
        {
            $firstSibling = TRUE;

            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        foreach($data as $key => $value)
        {
            if (!empty($firstSibling))
            {
                $key = 'package';
            }
            else
            {
                if (is_numeric($key))
                {
                    $key = "node_". (string) $key;
                }

                $key = preg_replace('/[^a-z]/i', '', $key);
            }

            if (is_array($value))
            {
                $node = $xml->addChild($key);

                self::toXml($value, $rootNodeName, $node);
            }
            else
            {
                $value = htmlentities($value);

                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }
    
    protected static function fromXML($xml)
    {
        $return = array();

        if ($xml instanceof SimpleXMLElement)
        {
            $children = $xml->children();
        }
        else
        {
            $xml = simplexml_load_string($xml);

            $children = $xml->children();
        }

        foreach ($children as $element => $value)
        {
            $values = (array)$value->children();

            if (count($values) > 0)
            {
                $return[$element] = self::fromXML($value);
            }
            else if (!isset($return[$element]))
            {
                $return[$element] = (string)$value;
            }
            else if (!is_array($return[$element]))
            {
                $return[$element] = array($return[$element], (string)$value);
            }
            else
            {
                $return[$element][] = (string)$value;
            }
        }

        return $return;
    }
}