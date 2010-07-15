<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Dependency
{
    public static function validateIntegration($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        $failures = array();

        foreach($package['required'] as $requirement => $conditions)
        {
            switch($requirement)
            {
                case 'not':
                    foreach($conditions as $name => $condition)
                    {
                        if (!$package = Package_Catalog::getInstalledPackage($name))
                        {
                            continue;
                        }

                        if (self::compareVersion($package['version'], $condition))
                        {
                            kohana::log('debug', 'Dependency error: ' .$package['packageName'] .' can not be installed with ' .$name .' version ' .$condition);
                            
                            $failures['not'][$name] = $condition;
                        }
                    }
                    
                    break;

                case 'or':
                    $failed = array();

                    foreach($conditions as $name => $condition)
                    {
                        if (!$required = Package_Catalog::getInstalledPackage($name))
                        {
                            continue;
                        }

                        if (self::compareVersion($required['version'], $condition))
                        {
                            continue 2;
                        }
                        
                        $failed[$name] = $condition;
                    }

                    kohana::log('debug', 'Dependency error: ' .$package['packageName'] .' requires one to be installed -> ' .implode(', ', $failed));

                    $failures['or'] += $failed;
                    
                    break;

                default:
                    if (!$required = Package_Catalog::getInstalledPackage($requirement))
                    {
                        kohana::log('debug', 'Dependency error: ' .$package['packageName'] .' requires ' .$requirement .' version ' .$conditions .' but it isnt installed');

                        $failures['missing'][$requirement] = $conditions;

                        continue;
                    }

                    if (!self::compareVersion($required['version'], $conditions))
                    {
                        kohana::log('debug', 'Dependency error: ' .$package['packageName'] .' requires ' .$requirement .' version ' .$conditions);

                        $failures['incompatible'][$requirement] = $conditions;
                    }
            }
        }

        if (!empty($failures))
        {
            $dependencyException = new Package_Dependency_Exception('Package did not pass dependency validation');

            $dependencyException->loadFailures($failures);

            throw $dependencyException;
        }
    }

    public static function validateAbandon($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (!empty($package['denyRemoval']))
        {
            kohana::log('debug', 'Dependency error: ' .$parentName .' has the denyRemoval flag set');

            throw new Package_Dependency_Exception('Package is not eligible for removal');
        }

        $dependents = self::listDependents($package['packageName']);

        $failures = array();

        foreach($dependents as $dependent)
        {
            if ($dependentPackage = Package_Catalog::getInstalledPackage($dependent))
            {
                kohana::log('debug', 'Dependency error: ' .$package['packageName'] .' is being used by ' .$dependent .' version ' . $dependentPackage['version']);

                $failures['indispensable'][$dependent] = $dependentPackage['version'];
            }
        }

        if (!empty($failures))
        {
            $dependencyException = new Package_Dependency_Exception('Package did not pass dependency validation');

            $dependencyException->loadFailures($failures);

            throw $dependencyException;
        }
    }

    public static function compareVersion($avaliableVersion, $requiredVersion, $operator = '>=')
    {
        $validOperators = array(
            '!=', '<>', 'ne',
            '<=', 'le',
            '<', 'lt',
            '>=', 'ge',
            '>', 'gt',
            '==', 'eq'
        );

        if (count($logic = explode(' and ', $requiredVersion)) == 2)
        {
            return self::compareVersion($avaliableVersion, $logic[0], $operator)
                    && self::compareVersion($avaliableVersion, $logic[1], $operator);
        }

        if (count($logic = explode(' or ', $requiredVersion)) == 2)
        {
            return self::compareVersion($avaliableVersion, $logic[0], $operator)
                    || self::compareVersion($avaliableVersion, $logic[1], $operator);
        }

        // This might seem odd but if the operator is in the version strings there has to be a space....
        if (strstr($requiredVersion, ' '))
        {
            // Check the strings for a valid operator
            foreach($validOperators as $validOperator)
            {
                $validOperator .= ' ';
                
                if (stristr($requiredVersion, $validOperator))
                {
                    $requiredVersion = str_replace($validOperator, '', $requiredVersion);

                    $operator = str_replace(' ', '', $validOperator);

                    break;
                }
            }
        }
        
        // make the comparision
        return version_compare($avaliableVersion, $requiredVersion, $operator);
    }

    protected static function listDependents($parentPackage)
    {
        $catalog = Package_Catalog::getCatalog();

        $dependents = array();

        foreach ($catalog as $id => $package)
        {
            $dependent = $package['packageName'];
            
            foreach($package['required'] as $requirement => $conditions)
            {
                switch($requirement)
                {
                    case 'not':
                        continue;

                    case 'or':
                        foreach($conditions as $name => $condition)
                        {
                            if ($name == $parentPackage)
                            {
                                if (!in_array($dependent, $dependents))
                                {
                                    $dependents[] = $dependent;
                                }
                            }
                        }

                        break;

                    default:
                        if ($requirement == $parentPackage)
                        {
                            if (!in_array($dependent, $dependents))
                            {
                                $dependents[] = $dependent;
                            }
                        }
                }
            }
        }

        return $dependents;
    }
}