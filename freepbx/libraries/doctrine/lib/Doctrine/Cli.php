<?php
/*
 *  $Id: Cli.php 2761 2007-10-07 23:42:29Z zYne $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Command line interface class
 * Interface for easily executing Doctrine_Task classes from a 
 * command line interface
 *
 * @package     Doctrine
 * @subpackage  Cli
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 2761 $
 * @author      Jonathan H. Wage <jwage@mac.com>
 */
class Doctrine_Cli
{
    protected $_tasks        = array(),
              $_taskInstance = null,
              $_formatter    = null,
              $_scriptName   = null,
              $_message      = null,
              $_config       = array();

    /**
     * __construct
     *
     * @param string $config 
     * @return void
     */
    public function __construct($config = array())
    {
        $this->_config = $config;
        $this->_formatter = new Doctrine_Cli_AnsiColorFormatter();
        
        $this->loadTasks();
    }

    /**
     * Notify the formatter of a message
     *
     * @param string $notification  The notification message
     * @param string $style         Style to format the notification with(INFO, ERROR)
     * @return void
     */
    public function notify($notification = null, $style = 'HEADER')
    {
        echo $this->_formatter->format($this->_taskInstance->getTaskName(), 'INFO') . ' - ' . $this->_formatter->format($notification, $style) . "\n";
    }

    /**
     * Notify the formatter of an exception
     *
     * @param  Exception $exception
     * @return void
     */
    public function notifyException($exception)
    {
        echo $this->_formatter->format($exception->getMessage(), 'ERROR') . "\n";
    }

    /**
     * Public function to run the loaded task with the passed arguments
     *
     * @param  array $args
     * @return void
     * @throws new Doctrine_Cli_Exception
     */
    public function run($args)
    {
        try {
            $this->_run($args);
        } catch (Exception $exception) {
            $this->notifyException($exception);
        }
    }

    /**
     * Get the name of the task class based on the first argument
     * which is always the task name. Do some inflection to determine the class name
     *
     * @param  array $args       Array of arguments from the cli
     * @return string $taskClass Task class name
     */
    protected function _getTaskClassFromArgs($args)
    {
        $taskName = str_replace('-', '_', $args[1]);
        $taskClass = 'Doctrine_Task_' . Doctrine_Inflector::classify($taskName);
        
        return $taskClass;
    }

    /**
     * Run the actual task execution with the passed arguments
     *
     * @param  array $args Array of arguments for this task being executed
     * @return void
     * @throws Doctrine_Cli_Exception $e
     */
    protected function _run($args)
    {        
        $this->_scriptName = $args[0];
        
        $arg1 = isset($args[1]) ? $args[1]:null;
        
        if ( ! $arg1 || $arg1 == 'help') {
            echo $this->printTasks(null, $arg1 == 'help' ? true:false);
            return;
        }
        
        if (isset($args[1]) && isset($args[2]) && $args[2] === 'help') {
            echo $this->printTasks($args[1], true);
            return;
        }
        
        $taskClass = $this->_getTaskClassFromArgs($args);
        
        if ( ! class_exists($taskClass)) {
            throw new Doctrine_Cli_Exception('Cli task could not be found: ' . $taskClass);
        }
        
        unset($args[0]);
        unset($args[1]);
        
        $this->_taskInstance = new $taskClass($this);
        
        $args = $this->prepareArgs($args);
        
        $this->_taskInstance->setArguments($args);
        
        try {
            if ($this->_taskInstance->validate()) {
                $this->_taskInstance->execute();
            } else {
                echo $this->_formatter->format('Requires arguments missing!!', 'ERROR') . "\n\n";
                echo $this->printTasks($arg1, true);
            }
        } catch (Exception $e) {
            throw new Doctrine_Cli_Exception($e->getMessage());
        }
    }

    /**
     * Prepare the raw arguments for execution. Combines with the required and optional argument
     * list in order to determine a complete array of arguments for the task
     *
     * @param  array $args      Array of raw arguments
     * @return array $prepared  Array of prepared arguments
     */
    protected function prepareArgs($args)
    {
        $taskInstance = $this->_taskInstance;
        
        $args = array_values($args);
        
        // First lets load populate an array with all the possible arguments. required and optional
        $prepared = array();
        
        $requiredArguments = $taskInstance->getRequiredArguments();
        foreach ($requiredArguments as $key => $arg) {
            $prepared[$arg] = null;
        }
        
        $optionalArguments = $taskInstance->getOptionalArguments();
        foreach ($optionalArguments as $key => $arg) {
            $prepared[$arg] = null;
        }
        
        // If we have a config array then lets try and fill some of the arguments with the config values
        if (is_array($this->_config) && !empty($this->_config)) {
            foreach ($this->_config as $key => $value) {
                if (array_key_exists($key, $prepared)) {
                    $prepared[$key] = $value;
                }
            }
        }
        
        // Now lets fill in the entered arguments to the prepared array
        $copy = $args;
        foreach ($prepared as $key => $value) {
            if ( ! $value && !empty($copy)) {
                $prepared[$key] = $copy[0];
                unset($copy[0]);
                $copy = array_values($copy);
            }
        }
        
        return $prepared;
    }

    /**
     * Prints an index of all the available tasks in the CLI instance
     * 
     * @return void
     */
    public function printTasks($task = null, $full = false)
    {
        $task = Doctrine_Inflector::classify(str_replace('-', '_', $task));
        
        $tasks = $this->getLoadedTasks();
        
        echo $this->_formatter->format("Doctrine Command Line Interface", 'HEADER') . "\n\n";
        
        foreach ($tasks as $taskName)
        {
            if ($task != null && strtolower($task) != strtolower($taskName)) {
                continue;
            }
            
            $className = 'Doctrine_Task_' . $taskName;
            $taskInstance = new $className();
            $taskInstance->taskName = str_replace('_', '-', Doctrine_Inflector::tableize($taskName));         
            
            $syntax = $this->_scriptName . ' ' . $taskInstance->getTaskName();
            
            echo $this->_formatter->format($syntax, 'INFO'); 
            
            if ($full) {
                echo " - " . $taskInstance->getDescription() . "\n";  
                
                $args = null;
                
                $requiredArguments = $taskInstance->getRequiredArgumentsDescriptions();
                
                if ( ! empty($requiredArguments)) {
                    foreach ($requiredArguments as $name => $description) {
                        $args .= $this->_formatter->format($name, "ERROR");
                        
                        if (isset($this->_config[$name])) {
                            $args .= " - " . $this->_formatter->format($this->_config[$name], 'COMMENT');
                        } else {
                            $args .= " - " . $description;
                        }
                        
                        $args .= "\n";
                    }
                }
            
                $optionalArguments = $taskInstance->getOptionalArgumentsDescriptions();
                
                if ( ! empty($optionalArguments)) {
                    foreach ($optionalArguments as $name => $description) {
                        $args .= $name . ' - ' . $description."\n";
                    }
                }
            
                if ($args) {
                    echo "\n" . $this->_formatter->format('Arguments:', 'HEADER') . "\n" . $args;
                }
            }
            
            echo "\n";
        }
    }

    /**
     * Load tasks from the passed directory. If no directory is given it looks in the default
     * Doctrine/Task folder for the core tasks.
     *
     * @param  mixed $directory   Can be a string path or array of paths
     * @return array $loadedTasks Array of tasks loaded
     */
    public function loadTasks($directory = null)
    {
        if ($directory === null) {
            $directory = Doctrine::getPath() . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Task';
        }
        
        $parent = new ReflectionClass('Doctrine_Task');
        
        $tasks = array();
        
        if (is_dir($directory)) {
            foreach ((array) $directory as $dir) {
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir),
                                                        RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($it as $file) {
                    $e = explode('.', $file->getFileName());
                    if (end($e) === 'php' && strpos($file->getFileName(), '.inc') === false) {
                    
                        $className = 'Doctrine_Task_' . $e[0];
                    
                        if ( ! class_exists($className)) {
                            require_once($file->getPathName());
                    
                            $class = new ReflectionClass($className);
                    
                            if ($class->isSubClassOf($parent)) {
                                $tasks[$e[0]] = $e[0];
                            }
                        }
                    }
                }
            }
        }

        $classes = get_declared_classes();
        foreach ($classes as $className) {
            $class = new Reflectionclass($className);
            if ($class->isSubClassOf($parent)) {
                $task = str_replace('Doctrine_Task_', '', $className);
                $tasks[$task] = $task;
            }
        }

        $this->_tasks = array_merge($this->_tasks, $tasks);
        
        return $this->_tasks;
    }

    /**
     * Get array of all the Doctrine_Task child classes that are loaded
     *
     * @return array $tasks
     */
    public function getLoadedTasks()
    {
        $parent = new ReflectionClass('Doctrine_Task');
        
        $classes = get_declared_classes();
        
        $tasks = array();
        
        foreach ($classes as $className) {
            $class = new ReflectionClass($className);
        
            if ($class->isSubClassOf($parent)) {
                $task = str_replace('Doctrine_Task_', '', $className);
                
                $tasks[$task] = $task;
            }
        }
        
        return array_merge($this->_tasks, $tasks);
    }
}