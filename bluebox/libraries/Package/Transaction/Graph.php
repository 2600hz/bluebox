<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Transaction_Graph
{
    protected static $installOrder = NULL;

    protected static $uninstallOrder = NULL;

    public static function sortInstall($packageA, $packageB)
    {
        try
        {
            $packageA = Package_Catalog::getPackageName($packageA);

            $packageB = Package_Catalog::getPackageName($packageB);

            return self::$installOrder[$packageA] - self::$installOrder[$packageB];
        }
        catch(Exception $e)
        {
            return -1;
        }
    }

    public static function sortUninstall($packageA, $packageB)
    {
        try
        {
            $packageA = Package_Catalog::getPackageName($packageA);

            $packageB = Package_Catalog::getPackageName($packageB);

            return self::$uninstallOrder[$packageA] - self::$uninstallOrder[$packageB];
        }
        catch(Exception $e)
        {
            return -1;
        }
    }

    public static function determineInstallOrder()
    {
        self::$installOrder = array();

        $requirements = self::listRequirements();

        $catalog = Package_Catalog::getCatalog();

        foreach ($catalog as $id => $package)
        {
            $reliance = self::graphReliance($requirements, $package['packageName']);

            self::$installOrder = arr::merge(self::$installOrder, $reliance);
        }

        self::$installOrder = array_unique(self::$installOrder);

        self::$installOrder = array_flip(self::$installOrder);
    }

    public static function determineUninstallOrder()
    {
        self::$uninstallOrder = array();

        $dependencies = self::listDependencies();

        $catalog = Package_Catalog::getCatalog();

        foreach ($catalog as $id => $package)
        {
            $reliance = self::graphReliance($dependencies, $package['packageName']);

            self::$uninstallOrder = arr::merge(self::$uninstallOrder, $reliance);
        }

        self::$uninstallOrder = array_unique(self::$uninstallOrder);

        self::$uninstallOrder = array_flip(self::$uninstallOrder);
    }

    protected static function listRequirements()
    {
        $catalog = Package_Catalog::getCatalog();

        $relianceList = array();

        foreach ($catalog as $id => $package)
        {
            $packageName = $package['packageName'];
            
            if (!array_key_exists($packageName, $relianceList))
            {
                $relianceList[$packageName] = array();
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
                            $relianceList[$packageName][] = $name;
                        }

                        break;

                    default:
                        $relianceList[$packageName][] = $requirement;
                }
            }
        }

        return $relianceList;
    }

    protected static function listDependencies()
    {
        $catalog = Package_Catalog::getCatalog();

        $dependencyList = array();

        foreach ($catalog as $id => $package)
        {
            $packageName = $package['packageName'];

            if (!array_key_exists($packageName, $dependencyList))
            {
                $dependencyList[$packageName] = array();
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
                            $dependencyList[$name][] = $packageName;
                        }

                        break;

                    default:
                        $dependencyList[$requirement][] = $packageName;
                }
            }
        }

        return $dependencyList;
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
    protected static function graphReliance ($graph, $node, &$resolved = array(), &$unresolved = array())
    {
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
}