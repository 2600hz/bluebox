<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Dependency_Graph
{
    public static function getDependents($parentPackage)
    {
        $graph = self::buildRelianceGraph();

        $reliantPackages = self::graphReliance($graph, $parentPackage);

        if (($self = array_search($parentPackage, $reliantPackages)) !== FALSE)
        {
            unset($reliantPackages[$self]);
        }

        return $reliantPackages;
    }

    public static function sortInstall($packages)
    {
//        $dependencies = $this->graphReliance($relianceGraph, $packageName);
//        $dependencies = array_flip($dependencies);
//        foreach ($dependencies as $key => $value) {
//            $dependencies[$key] = array($packageName);
//        }
//        $reliantPackages = array_merge_recursive($dependencies, $reliantPackages);
    }

    public static function sortUninstall($packages)
    {
//        $dependencies = $this->graphReliance($relianceGraph, $packageName);
//        $dependencies = array_flip($dependencies);
//        foreach ($dependencies as $key => $value) {
//            $dependencies[$key] = array($packageName);
//        }
//        $reliantPackages = array_merge_recursive($dependencies, $reliantPackages);
    }

    /**
     * Process a directed graph data structure and
     * determine the path and order necessary to install/uninstall.
     * Throws error if it can not be resolved (missing, circular, ect).
     *
     * @param array
     * @param string The packageName to find the dependency path for
     * @param array Internall array traking resolved dependencies
     * @param array Internall array traking seen dependencies for circular reference detection
     * @return array
     */
    protected static function graphReliance ($graph, $node, &$resolved = array(), &$unresolved = array()) {
        // Track a list of unresolved nodes for circular reference detection
        $unresolved[] = $node;

        // Find the edges of this graph node
        $edges = array();
        
        if (!empty($graph[$node]))
        {
            $edges = $graph[$node];
        }

        foreach (array_values($edges) as $edge)
        {
            // if this edge is not already on the list then add it
            if (array_search($edge, $resolved) === FALSE)
            {
                // if we have tried to resolve this before but couldnt then
                // it is a circular reference (they are looping between eachother)
                if (array_search($edge, $unresolved) !== FALSE)
                {
                    throw new Package_Dependency_Exception(
                            'Circular package dependency between '
                            .$this->displayName($edge) .' and '
                            .$this->displayName($node)
                    );
                }
                
                self::graphReliance($graph, $edge, $resolved, $unresolved);
            }
        }

        // If we successfully resolved this node (ie got here) remove it from
        // the unresolved list ...
         unset($unresolved[array_search($node, $unresolved)]);

        // ... and add it to the resolved
        $resolved[] = $node;

        return $resolved;
    }

    protected static function buildRelianceGraph()
    {
        $catalog = Package_Catalog::getCatalog();

        $relianceGraph = array();

        foreach ($catalog as $id => $package)
        {
            $packageName = $package['packageName'];

            if (!array_key_exists($packageName, $relianceGraph))
            {
                $relianceGraph[$packageName] = array();
            }

            foreach($package['required'] as $requirement => $conditions)
            {
                switch($requirement)
                {
                    case 'not':
                        continue;

                        break;

                    case 'or':
                        foreach($conditions as $name => $condition)
                        {
                            if (!array_key_exists($name, $relianceGraph))
                            {
                                $relianceGraph[$name] = array();
                            }

                            $relianceGraph[$name][] = $packageName;
                        }

                        break;

                    default:
                        $relianceGraph[$requirement][] = $packageName;
                }
            }
        }

        return $relianceGraph;
    }
}