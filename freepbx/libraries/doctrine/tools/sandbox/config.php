<?php
/*
 *  $Id: config.php 2753 2007-10-07 20:58:08Z Jonathan.Wage $
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
 * Doctrine Configuration File
 *
 * This is a sample implementation of Doctrine
 * 
 * @package     Doctrine
 * @subpackage  Config
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 2753 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Jonathan H. Wage <jwage@mac.com>
 */

define('SANDBOX_PATH', dirname(__FILE__));
define('DOCTRINE_PATH', dirname(dirname(SANDBOX_PATH)) . DIRECTORY_SEPARATOR . 'lib');
define('DATA_FIXTURES_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures');
define('MODELS_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'models');
define('MIGRATIONS_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'migrations');
define('SQL_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'sql');
define('YAML_SCHEMA_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'schema');
define('DB_PATH', SANDBOX_PATH . DIRECTORY_SEPARATOR . 'sandbox.db');
define('DSN', 'sqlite:///' . DB_PATH);

require_once(DOCTRINE_PATH . DIRECTORY_SEPARATOR . 'Doctrine.php');

Doctrine::setExtensionsPath(dirname(__FILE__).'/extensions');

spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register(array('Doctrine', 'modelsAutoload'));
spl_autoload_register(array('Doctrine', 'extensionsAutoload'));

$manager = Doctrine_Manager::getInstance();
$manager->openConnection(DSN, 'doctrine');
$manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);