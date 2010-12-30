<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Models
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Bluebox_Migration extends Doctrine_Migration
{
    /**
     * Doctrine's native migrations are setup to track the database as a whole,
     * and not individual tables... since we are a modular system we need
     * to bend it out of shape, again.
     */

    protected $_modelClass;

    /**
     * Specify the path to the directory with the migration classes.
     * The classes will be loaded and the migration table will be created if it
     * does not already exist
     *
     * @param string $directory The path to your migrations directory
     * @param mixed $connection The connection name or instance to use for this migration
     * @param string $modelClass The name of the class that we are tracking versions of
     * @return void
     */
    public function __construct($directory = null, $connection = null, $modelClass = NULL)
    {
        $this->_reflectionClass = new ReflectionClass('Doctrine_Migration_Base');

        $this->_modelClass = $modelClass;

        if (is_null($connection))
        {
            $this->_connection = Doctrine_Manager::connection();
        } 
        else
        {
            if (is_string($connection))
            {
                $this->_connection = Doctrine_Manager::getInstance()
                    ->getConnection($connection);
            } 
            else
            {
                $this->_connection = $connection;
            }
        }

        $this->_process = new Doctrine_Migration_Process($this);

        if ($directory != null)
        {
            $this->_migrationClassesDirectory = $directory;

            $this->loadMigrationClassesFromDirectory();

            $this->_createMigrationTable();
        }
    }

    /**
     * Set the current version of the database
     *
     * @param integer $number
     * @return void
     */
    public function setCurrentVersion($number)
    {
        if ($this->hasMigrated())
        {
            $this->_connection->exec("UPDATE " . $this->_migrationTableName . " SET version = $number WHERE model_class = '" .$this->_modelClass ."'");
        }
        else
        {
            $this->_connection->exec("INSERT INTO " . $this->_migrationTableName . " (model_class, version) VALUES ('" .$this->_modelClass ."', $number)");
        }
    }

    /**
     * Get the current version of the database
     *
     * @return integer $version
     */
    public function getCurrentVersion()
    {
        $result = $this->_connection->fetchColumn("SELECT version FROM " . $this->_migrationTableName ." WHERE model_class = '" .$this->_modelClass ."'");

        return isset($result[0]) ? $result[0]:0;
    }

    /**
     * hReturns true/false for whether or not this database has been migrated in the past
     *
     * @return boolean $migrated
     */
    public function hasMigrated()
    {
        $result = $this->_connection->fetchColumn("SELECT version FROM " . $this->_migrationTableName ." WHERE model_class = '" .$this->_modelClass ."'");

        return isset($result[0]) ? true:false;
    }

    /**
     * Create the migration table and return true. If it already exists it will
     * silence the exception and return false
     *
     * @return boolean $created Whether or not the table was created. Exceptions
     *                          are silenced when table already exists
     */
    protected function _createMigrationTable()
    {
        try
        {
            $this->_connection->export->createTable($this->_migrationTableName, array(
                    'model_class' => array('type' => 'string', 'size' => 75),
                    'version' => array('type' => 'integer', 'size' => 11)
                )
            );

            return true;
        } 
        catch(Exception $e)
        {
            return false;
        }
    }
}