<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Catalog_Remote
{
    public static function queryRepositories($repos = NULL)
    {
        if (is_null($repos))
        {
            $repos = kohana::config('core.repositories');
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
            $repoXMLCatalog = self::fetch($repo);

            $remoteCatalog = self::fromXML($repoXMLCatalog);

            foreach ($remoteCatalog as $package)
            {
                if (empty($package['identifier']))
                {
                    continue;
                }

                if (array_key_exists($package['identifier'], $remoteCatalog))
                {
                    continue;
                }

                $package['status'] = Package_Manager::STATUS_UNINSTALLED;

                $remoteCatalogs[$package['identifier']] = $package;
            }
        }

        return $remoteCatalogs;
    }

    public function createRepo()
    {
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

        return file_get_contents($URL);
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