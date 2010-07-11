<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Bluebox_Configure
{
    /**
     * Version of this module. Use integers/decimals only please.
     * @var float
     */
    public static $version = 0.0;

    /**
     * The display name of this module
     * @var string
     */
    public static $displayName = NULL;

    /**
     * The string to display as the name of the author(s)
     * @var string
     */
    public static $author = 'Unspecified';

    /**
     * The string to display as the name organization
     * @var string
     */
    public static $vendor = 'Unspecified';

    /**
     * The string to display as the type of license
     * @var string
     */
    public static $license = 'MPL';

    /**
     * A breif description of the module, should to be under 150 charaters
     * Used in module listings to brief the user to the purpose of the module
     * @var string
     */
    public static $summary = 'This is a module for Bluebox.';

    /**
     * The full description of the module, used to provide users with more details
     * @var string
     */
    public static $description = '';

    /**
     * The default enabled value
     * @var bool
     */
    public static $default = FALSE;

    /**
     * If false this module can not be disabled
     * @var bool
     */
    public static $denyDisable = FALSE;
    
    /**
     * If false this module can not be uninstalled
     * @var bool
     */
    public static $denyRemoval = FALSE;

    /**
     * This is a list of catagories constants as defined in Core_PackageManager
     * @var string
     */
    public static $type = Bluebox_PackageManager::TYPE_DEFAULT;

    /**
     * This variable is useful for making sure the user has other modules installed that may be required
     * @var array Key/value pair of modules and dependencies
     */
    public static $required = array();

    /**
     *
     * @var string
     */
    public static $navBranch = '/';

    /**
     *
     * @var string
     */
    public static $navURL = NULL;

    /**
     *
     * @var string
     */
    public static $navLabel = NULL;

    /**
     *
     * @var string
     */
    public static $navSummary = NULL;

    /**
     * This is an array optionally defining a submenu
     * @var array
     */
    public static $navSubmenu = array();
    
    /**
     * The constuctor for this class ensures that the modules manditory defaults are fulfilled
     * @return void
     */
    public function __construct()
    {
        $this->noMethodMethod(__FUNCTION__);
    }

    public function sanityCheck()
    {
        $this->noMethodMethod(__FUNCTION__);
    }

    public function completedInstall()
    {
        $this->noMethodMethod(__FUNCTION__);
    }

    /**
     * Verify module is intact.
     *
     * Verify that this module has all it's parts and nothing looks out of whack.
     * This gets called by install, upgrade, downgrade, repair and sanityCheck.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function verify()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /*************************
    * INSTALLATION ROUTINES *
    *************************/
    /**
     * Do any pre-installation preparation.
     *
     * Things like making a temp directory or downloading stuff you might need.
     * All modules have their preInstall() methods run first, before install() runs, so if you may need to setup
     * something another module's install method depends on, do it here.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function preInstall()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
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
    public function install($package = NULL)
    {
        // If this package has any models, load them and determine which ones are BASE models (i.e. not extensions of other models)
        // Note that we do this because Postgers & Doctrine don't like our polymorphic class extensions and try to create the same
        // tables twice.
        $models = array();

        if (!empty($package['models']))
        {
            foreach($package['models'] as $className)
            {
                if ((get_parent_class($className) == 'Bluebox_Record') or (get_parent_class($className) == 'Doctrine_Record'))
                {
                    $models[] = $className;
                }
            }
        }
        
        // If this package has any models of it's own (not extensions) then create the tables!
        if (!empty($models))
        {
            Doctrine::createTablesFromArray($models);
        }
    }
    
    /**
     * Post-install routine for this module.
     *
     * Do any post-installation configuration that should happen after ALL modules have installed.
     * This is the safest place to do things that may depend on other modules.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function postInstall()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /********************
    * UPGRADE ROUTINES *
    ********************/
    /**
     * Do any pre-upgrade preparation.
     *
     * Things like making a temp directory or downloading stuff you might need.
     * All modules have their preUpgrade() methods run first, before upgrade() runs, so if you may need to setup
     * something another module's install method depends on, do it here.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function preUpgrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**
     * Perform an upgrade of this module.
     *
     * Make sure you rollback any changes if your install fails (using uninstall())!
     *
     * By default, the upgrade routine just runs the migrations in Doctrine for your models, based on the version of
     * this module (assumed to be the new version) and the version registered in the database (assumed to be the old version).
     * If that's all you need for your upgrade, you don't need to override this function.
     * All models in the directory of your module will be upgraded.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function upgrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**
     * Post-upgrade routine for this module.
     *
     * Do any post-upgrade configuration that should happen after ALL modules have upgraded.
     * This is the safest place to do things that may depend on other modules.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function postUpgrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**********************
    * DOWNGRADE ROUTINES *
    **********************/
    /**
     * Do any pre-downgrade preparation.
     *
     * Things like making a temp directory or downloading stuff you might need.
     * All modules have their preDowngrade() methods run first, before downgrade() runs, so if you may need to setup
     * something another module's install method depends on, do it here.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function preDowngrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**
     * Perform a downgrade of this module.
     *
     * Make sure you rollback any changes if your install fails (using uninstall())!
     *
     * By default, the upgrade routine just runs the migrations in Doctrine for your models, based on the version of
     * this module (assumed to be the new version) and the version registered in the database (assumed to be the old version).
     * If that's all you need for your upgrade, you don't need to override this function.
     * All models in the directory of your module will be upgraded.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function downgrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**
     * Post-downgrade routine for this module.
     *
     * Do any post-downgrade configuration that should happen after ALL modules have downgraded.
     * This is the safest place to do things that may depend on other modules.
     *
     * You do not need to override this class if you are not adding additional functionality to it.
     *
     * @return array | NULL Array of failures, or NULL if everything is OK
     */
    public function postDowngrade()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**********************
    * UNINSTALL ROUTINES *
    **********************/
    public function preUninstall()
    {
        $this->noMethodMethod(__FUNCTION__);
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
    public function uninstall($package = NULL)
    {
        $tables = array();
        
        // Get the doctrine overlord
        try
        {
            $conn = Doctrine_Manager::connection();
        }
        catch(Exception $e)
        {
            Kohana::log('error', 'Uninstall unable to get the overlord!');
            
            return 'Unable to connect to the database!';
        }

        // For each of this modules models loop through all of their rows and delete them
        // This will ensure any relationships are broken safely, but is a brute force approach...
        $models = $package['models'];

        foreach($models as $model)
        {
            try
            {
                $table = Doctrine::getTable($model);

                $tableName = $table->getOption('tableName');
                
                $declaringClass = $table->getOption('declaringClass');
            }
            catch(Exception $e)
            {
                Kohana::log('debug', 'Uninstall skipping model ' . $model . ', doesnt seem to have a doctrine table.');
                
                continue;
            }

            if (!$conn->import->tableExists($tableName))
            {
                continue;
            }
            
            if (!in_array($declaringClass->name, $models))
            {
                Kohana::log('alert', 'UNINSTALL REMOVING ' . $package['packageName'] . ' FROM TABLE ' . $model);

                $rows = Doctrine_Query::create()->from($model . ' t')->execute();

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
                Kohana::log('alert', 'UNINSTALL TRUNCATING TABLE: ' . $tableName);

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

                if (empty($relatedClass) || in_array($relatedClass, $models))
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

                        Kohana::log('alert', 'UNINSTALL DROPING TABLE: ' . $drop);

                        unset($tables[$key]);
                    }
                    catch(Exception $e)
                    {
                    }
                }

                $errorCount = $errorCount - 1;

                if ($errorCount < 0)
                {
                    Kohana::log('error', 'Uninstall unable to resolve drop table order!');

                    return 'Unable to resolve drop table order!';
                }
            }
        }
    }

    public function postUninstall()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**********************
    * ENABLE ROUTINES *
    **********************/
    public function enable()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /**********************
    * DISABLE ROUTINES *
    **********************/
    public function disable()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    /************************
    * MAINTENANCE ROUTINES *
    ************************/
    /**
     * What the heck should we do here by default? No idea... Maybe this is forced to be defined.
     */
    public function repair()
    {
        $this->noMethodMethod(__FUNCTION__);
    }
    
    private function noMethodMethod($method)
    {
        $class = str_replace('_Configure', '', get_class($this));

        Kohana::log('debug', "$class doesn't have a $method method");
    }
}
