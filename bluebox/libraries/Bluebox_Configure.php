<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
abstract class Bluebox_Configure  extends Package_Configure
{   
    /**
     * Do the actual installation.
     *
     * Make sure you rollback any changes if your install fails (using uninstall())!
     *
     * By default, the install routine just installs your models. If that's all you need for your install,
     * you don't need to override this function. All models in the directory of your module will be installed.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function install($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (!empty($package['directory']) AND $package['type'] != Package_Manager::TYPE_CORE)
        {
            kohana::log('debug', 'Dynamically adding `' .$package['directory'] .'` to kohana');

            $loadedModules = Kohana::config('core.modules');

            $modules = array_unique(array_merge($loadedModules, array($package['directory'])));

            Kohana::config_set('core.modules', $modules);
        }

        // If this package has any models, load them and determine which ones are BASE models (i.e. not extensions of other models)
        // Note that we do this because Postgers & Doctrine don't like our polymorphic class extensions and try to create the same
        // tables twice.
        if (!empty($package['models']))
        {
            $loadedModels = Doctrine::loadModels($package['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }

        if (!empty($loadedModels))
        {
            $models = array();

            foreach($loadedModels as $modelName)
            {
                if ((get_parent_class($modelName) == 'Bluebox_Record') or (get_parent_class($modelName) == 'Doctrine_Record'))
                {
                    $models[] = $modelName;
                }
            }
            
            // If this package has any models of it's own (not extensions) then create the tables!
            if (!empty($models))
            {
                kohana::log('debug', 'Adding table(s) ' .implode(', ', $models));

                Doctrine::createTablesFromArray($models);
            }
        }
    }

    /**
     * Perform a migration of this module.
     *
     * Make sure you rollback any changes if your migration fails!
     *
     * By default, the migrate routine just runs the migrations in Doctrine for your models, based on the version of
     * this module and the version registered in the database.
     * If that's all you need for your migrations, you don't need to override this function.
     * All models in the directory of your module will be migrated.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function migrate($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (!empty($package['models']))
        {
            $loadedModels = Doctrine::loadModels($package['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }

        if (empty($loadedModels))
        {
            return;
        }

        $installed = Package_Catalog::getInstalledPackage($package['packageName']);

        if (Package_Dependency::compareVersion($package['version'], $installed['version']))
        {
            kohana::log('debug', 'Attempting to upgrade package ' .$installed['packageName'] .' version ' .$installed['version'] .' to ' .$package['version']);

            foreach($loadedModels as $modelName)
            {
                if ((get_parent_class($modelName) != 'Bluebox_Record') AND (get_parent_class($modelName) != 'Doctrine_Record'))
                {
                    continue;
                }

                $migrationDirectory = $package['directory'] .'/migrations/' .$modelName;

                kohana::log('debug', 'Looking for migrations in `' .$migrationDirectory .'`');

                if (is_dir($migrationDirectory))
                {
                    try
                    {
                        $migration = new Bluebox_Migration($migrationDirectory, NULL, strtolower($modelName));

                        kohana::log('debug', 'Running migration on ' .$modelName .' from model version ' .$migration->getCurrentVersion() .' to ' .$migration->getLatestVersion());

                        $migration->migrate();

                        $msg = inflector::humanizeModelName($modelName);

                        $msg .= ' database table upgraded to model version # ' .$migration->getCurrentVersion();

                        Package_Message::set($msg, 'info', $identifier);
                    }
                    catch (Exception $e)
                    {
                        kohana::log('alert', 'Alerts during migration, this can USUALLY be ignored: ' .$e->getMessage());

                        // TODO: This isnt a great idea, but migrations are so noisy with needless failures... PITA
                        $migration->setCurrentVersion($migration->getLatestVersion());

                        foreach ($migration->getErrors() as $error)
                        {
                            if (strstr($error->getMessage(), 'Already at version'))
                            {
                                $msg = inflector::humanizeModelName($modelName);

                                $msg .= ' database table ' .inflector::lcfirst($error->getMessage());

                                Package_Message::set($msg, 'info', $identifier);
                            }
                            else
                            {
                                Package_Message::set($error->getMessage(), 'alert', $identifier);
                            }
                        }
                    }
                }
                else
                {
                   $migration = new Bluebox_Migration(NULL, NULL, strtolower($modelName));

                   $migration->setCurrentVersion(0);
                }
            }
        }
        else
        {
            kohana::log('debug', 'Attempting to downgrade package ' .$installed['packageName'] .' version ' .$installed['version'] .' to ' .$package['version']);

            foreach($loadedModels as $modelName)
            {
                if ((get_parent_class($modelName) != 'Bluebox_Record') AND (get_parent_class($modelName) != 'Doctrine_Record'))
                {
                    continue;
                }

                $migrationDirectory = $installed['directory'] .'/migrations/' .$modelName;

                kohana::log('debug', 'Looking for migrations in `' .$migrationDirectory .'`');

                if (is_dir($migrationDirectory))
                {
                    try
                    {
                        $modelVersion = 0;

                        if (is_dir($package['directory'] .'/migrations/' .$modelName))
                        {
                            $previousMigration = new Doctrine_Migration($package['directory'] .'/migrations/' .$modelName);

                            $modelVersion = $previousMigration->getLatestVersion();
                        }

                        kohana::log('debug', 'Determined that ' .$package['packageName'] .' version ' .$package['version'] .' works against ' .$modelName .' version ' .$modelVersion);

                        $migration = new Bluebox_Migration($migrationDirectory, NULL, strtolower($modelName));

                        kohana::log('debug', 'Running migration on ' .$modelName .' from model version ' .$migration->getCurrentVersion() .' to ' .$modelVersion);

                        $migration->migrate($modelVersion);

                        $msg = inflector::humanizeModelName($modelName);

                        $msg .= ' database table downgraded to model version # ' .$migration->getCurrentVersion();

                        Package_Message::set($msg, 'info', $identifier);
                    }
                    catch (Exception $e)
                    {
                        kohana::log('alert', 'Alerts during migration, this can USUALLY be ignored: ' .$e->getMessage());

                        // TODO: This isnt a great idea, but migrations are so noisy with needless failures... PITA
                        $migration->setCurrentVersion($migration->getLatestVersion());

                        foreach ($migration->getErrors() as $error)
                        {
                            if (strstr($error->getMessage(), 'Already at version'))
                            {
                                $msg = inflector::humanizeModelName($modelName);

                                $msg .= ' database table ' .inflector::lcfirst($error->getMessage());

                                Package_Message::set($msg, 'info', $identifier);
                            }
                            else
                            {
                                Package_Message::set($error->getMessage(), 'alert', $identifier);
                            }
                        }
                    }
                }
                else
                {
                   $migration = new Bluebox_Migration(NULL, NULL, strtolower($modelName));

                   $migration->setCurrentVersion(0);
                }
            }
        }
    }

    /**
     * Removes your module.
     *
     * This method MUST WORK as a last-resort rollback method on a botched install.
     *
     * By default, Core_PackageManager will remove any models associated with this module and then remove all files
     * in the module's directory. It will also remove the module entry from the Modules and ModuleUser tables.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function uninstall($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        $tables = array();

        if (!empty($package['models']))
        {
            $loadedModels = Doctrine::loadModels($package['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }

        if (empty($loadedModels))
        {
            return;
        }        
        
        // Get the doctrine overlord
        try
        {
            $conn = Doctrine_Manager::connection();
        }
        catch(Exception $e)
        {
            Kohana::log('error', 'Unable to get the doctrine overlord!');

            throw new Exception('Unable to connect to the database!');
        }

        // For each of this modules models loop through all of their rows and delete them
        // This will ensure any relationships are broken safely, but is a brute force approach...
        foreach($loadedModels as $modelName)
        {
            try
            {
                $reflection = new ReflectionClass($modelName);

                if ($reflection->isSubclassOf('Doctrine_Template'))
                {
                    throw new Exception('Model is a doctrine template');
                }

                $table = Doctrine::getTable($modelName);

                $tableName = $table->getOption('tableName');

                if (!$conn->import->tableExists($tableName))
                {
                    throw new Exception('Table does not exist');
                }

                $declaringClass = $table->getOption('declaringClass');
            }
            catch(Exception $e)
            {
                Kohana::log('debug', 'Skipping uninstall on model ' . $modelName . ': ' .$e->getMessage());

                continue;
            }

            if (!in_array($declaringClass->name, $loadedModels))
            {
                Kohana::log('debug', 'Removing ' . $package['packageName'] . ' from table ' . $modelName);

                $rows = Doctrine_Query::create()->from($modelName . ' t')->execute();

                foreach($rows as $row)
                {
                    $row->class_type = null;

                    $row->foreign_id = null;

                    $row->save();
                }

                continue;
            }
            else
            {
                Kohana::log('debug', 'Truncating table ' . $tableName);

                $rows = $table->findAll();

                foreach($rows as $row)
                {
                    $row->delete();
                }

                $tables[] = $tableName;
            }

            $relations = array_keys($table->getRelations());

            foreach($relations as $relation)
            {
                $related = Doctrine::getTable($relation);

                $relatedTable = $related->getOption('tableName');

                $relatedClass = $related->getOption('declaringClass');

                if (empty($relatedClass) || in_array($relatedClass, $loadedModels))
                {
                    $tables[] = $relatedTable;
                }
            }
        }

        if (!empty($tables))
        {
            $tables = array_unique($tables);

            $removeCount = count($tables);

            $errorCount = ($removeCount * $removeCount) + $removeCount;

            while (!empty($tables))
            {
                foreach($tables as $key => $drop)
                {
                    if (!$conn->import->tableExists($drop))
                    {
                        unset($tables[$key]);

                        continue;
                    }

                    try
                    {
                        $conn->export->dropTable($drop);

                        Kohana::log('debug', 'Drop ' .$drop .' like its hot');

                        unset($tables[$key]);
                    }
                    catch(Exception $e)
                    {
                    }
                }

                $errorCount = $errorCount - 1;

                if ($errorCount < 0)
                {
                    Kohana::log('error', 'Unable to resolve drop table order!');

                    throw new Exception('Unable to resolve drop table order!');
                }
            }
        }
    }

    public function repair($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (!empty($package['models']))
        {
            $loadedModels = Doctrine::loadModels($package['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }

        if (empty($loadedModels))
        {
            return;
        }

        foreach($loadedModels as $modelName)
        {
            if ((get_parent_class($modelName) != 'Bluebox_Record') AND (get_parent_class($modelName) != 'Doctrine_Record'))
            {
                continue;
            }

            $migrationDirectory = $package['directory'] .'/migrations/' .$modelName;

            kohana::log('debug', 'Looking for migrations in `' .$migrationDirectory .'`');

            if (is_dir($migrationDirectory))
            {
                try
                {
                    kohana::log('debug', 'Setting ' .$modelName .' to version 0 and walking migrations forward to ensure table schema');

                    $migration = new Bluebox_Migration($migrationDirectory, NULL, strtolower($modelName));

                    $migration->setCurrentVersion(0);

                    $migration->migrate();
                }
                catch(Exception $e)
                {
                    kohana::log('alert', 'Alerts during migration, this can USUALLY be ignored: ' .$e->getMessage());

                    foreach ($migration->getErrors() as $error)
                    {
                        kohana::log('alert', $error->getMessage());
                    }
                }
            }
        }
    }
}