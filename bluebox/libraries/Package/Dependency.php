<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
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
                        if (!$dependency = Package_Catalog::getInstalledPackage($name))
                        {
                            try
                            {
                                // Hmmm, this is offly optimistic....
                                $dependency = Package_Transaction::checkTransaction($name);
                            }
                            catch(Exception $e)
                            {
                                continue;
                            }
                        }

                        if (self::compareVersion($dependency['version'], $condition))
                        {
                            Package_Message::log('debug', 'dependency restriction, ' .$package['packageName'] .' can not be installed with ' .$name .' version ' .$condition);

                            Package_Message::set($package['displayName'] .' version ' .$package['version'] .' can not be installed with ' .$dependency['displayName'] .' version ' .$dependency['version'], 'error', $identifier);

                            Package_Message::set($dependency['displayName'] .' conflicts with ' .$package['displayName'], 'alert', $dependency['identifier']);

                            $failures['not'][$name] = $condition;
                        }
                    }
                    
                    break;

                case 'or':
                    $failed = array();

                    foreach($conditions as $name => $condition)
                    {
                        if (!$dependency = Package_Catalog::getInstalledPackage($name))
                        {
                            try
                            {
                                // Hmmm, this is offly optimistic....
                                $dependency = Package_Transaction::checkTransaction($name);
                            }
                            catch(Exception $e)
                            {
                                continue;
                            }
                        }

                        if (self::compareVersion($dependency['version'], $condition))
                        {
                            continue 2;
                        }

                        Package_Message::set($dependency['displayName'] .' is part of a series of packages, one of which must be installed for ' .$package['displayName'], 'info', $dependency['identifier']);

                        $failed[$name] = $condition;
                    }

                    Package_Message::log('debug', 'dependency restriction ' .$package['packageName'] .' requires one to be installed -> ' .implode(', ', $failed));

                    Package_Message::set($package['displayName'] .' version ' .$package['version'] .' requires one of the following to be install: ' .implode(', ', $failed), 'error', $identifier);

                    $failures['or'] += $failed;
                    
                    break;

                default:
                    if (!$dependency = Package_Catalog::getInstalledPackage($requirement))
                    {
                        try
                        {
                            // Hmmm, this is offly optimistic....
                            $dependency = Package_Transaction::checkTransaction($requirement);
                        }
                        catch(Exception $e)
                        {
                            Package_Message::log('debug', 'dependency restriction ' .$package['packageName'] .' requires ' .$requirement .' version ' .$conditions .' but it is not installed');

                            Package_Message::set($package['displayName'] .' version ' .$package['version'] .' requires ' .ucfirst($requirement) .' but it is not avaliable', 'error', $identifier);

                            $failures['missing'][$requirement] = $conditions;

                            continue;
                        }                        
                    }

                    if (!self::compareVersion($dependency['version'], $conditions))
                    {
                        Package_Message::log('debug', 'dependency restriction ' .$package['packageName'] .' requires ' .$requirement .' version ' .$conditions);

                        Package_Message::set($package['displayName'] .' version ' .$package['version'] .' requires ' .$dependency['displayName'] .' version ' . $conditions .' but it is not avaliable', 'error', $identifier);

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
            Package_Message::log('debug', 'denyRemoval flag set on package ' .$package['packageName']);

            Package_Message::set($package['displayName'] .' is not eligible for removal', 'error', $identifier);

            throw new Package_Dependency_Exception('Package is not eligible for removal');
        }

        $dependents = self::listDependents($package['packageName']);

        $failures = array();

        foreach($dependents as $dependent)
        {
            if ($dependentPackage = Package_Catalog::getInstalledPackage($dependent))
            {
                Package_Message::log('debug', 'dependency restriction ' .$package['packageName'] .' is being used by ' .$dependent .' version ' . $dependentPackage['version']);

                Package_Message::set($package['displayName'] .' is being used by ' .$dependentPackage['displayName'] .' version ' . $dependentPackage['version'], 'error', $identifier);

                Package_Message::set($dependentPackage['displayName'] .' is using ' .$package['displayName'], 'alert', $dependentPackage['identifier']);

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
            if ($package['status'] == Package_Manager::STATUS_UNINSTALLED)
            {
                continue;
            }

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
