<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Dependency
{
    public static function validateIntegration($identifier)
    {
        //var_dump(self::compareVersion('0.6', '< 0.1 or > 0.5'));
  
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
                            $failures['not'][$name] = $condition;
                        }
                    }
                    
                    break;

                case 'or':
                    $failed = array();

                    foreach($conditions as $name => $condition)
                    {
                        if (!$package = Package_Catalog::getInstalledPackage($name))
                        {
                            continue;
                        }

                        if (self::compareVersion($package['version'], $condition))
                        {
                            continue 2;
                        }
                        
                        $failed[$name] = $condition;
                    }

                    $failures['or'] += $failed;
                    
                    break;

                default:
                    if (!$package = Package_Catalog::getInstalledPackage($requirement))
                    {
                        $failures['missing'][$requirement] = $conditions;

                        continue;
                    }

                    if (!self::compareVersion($package['version'], $conditions))
                    {
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
}