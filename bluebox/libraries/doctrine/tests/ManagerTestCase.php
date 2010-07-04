<?php
/*
 *  $Id$
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
 * Doctrine_Manager_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Manager_TestCase extends Doctrine_UnitTestCase {
    public function testGetInstance() {
        $this->assertTrue(Doctrine_Manager::getInstance() instanceOf Doctrine_Manager);
    }
    public function testOpenConnection() {
        $this->assertTrue($this->connection instanceOf Doctrine_Connection);
    }
    public function testGetIterator() {
        $this->assertTrue($this->manager->getIterator() instanceof ArrayIterator);
    }
    public function testCount() {
        $this->assertTrue(is_integer(count($this->manager)));
    }
    public function testGetCurrentConnection() {
        $this->assertTrue($this->manager->getCurrentConnection() === $this->connection);
    }
    public function testGetConnections() {
        $this->assertTrue(is_integer(count($this->manager->getConnections())));
    }
    public function testClassifyTableize() {
        $name = "Forum_Category";
        $this->assertEqual(Doctrine_Inflector::tableize($name), "forum__category");
        $this->assertEqual(Doctrine_Inflector::classify(Doctrine_Inflector::tableize($name)), $name);
        
        
    }
    public function testDsnParser()
    {
        $mysql = 'mysql://user:pass@localhost/dbname';
        $sqlite = 'sqlite:////full/unix/path/to/file.db';
        $sqlitewin = 'sqlite:///c:/full/windows/path/to/file.db';
        $sqlitewin2 = 'sqlite:///D:\full\windows\path\to\file.db';
        
        $manager = Doctrine_Manager::getInstance();
        
        try {
            $res = $manager->parseDsn($mysql);
            $expectedMysqlDsn = array(
                "scheme" => "mysql",
                "host" => "localhost",
                "user" => "user",
                "pass" => "pass",
                "path" => "/dbname",
                "dsn" => "mysql:host=localhost;dbname=dbname",
                "port" => NULL,
                "query" => NULL, 
                "fragment" => NULL,
                "unix_socket" => NULL,
                "database" => "dbname");
            $this->assertEqual($expectedMysqlDsn, $res);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        try {
            $expectedDsn = array(
                "scheme" => "sqlite",
                "host" => NULL,
                "user" => NULL,
                "pass" => NULL,
                "path" => "/full/unix/path/to/file.db",
                "dsn" => "sqlite:/full/unix/path/to/file.db",
                "port" => NULL,
                "query" => NULL, 
                "fragment" => NULL,
                "unix_socket" => NULL,
                "database" => "/full/unix/path/to/file.db");
              
            $res = $manager->parseDsn($sqlite);
            $this->assertEqual($expectedDsn, $res);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        
        try {
             $expectedDsn = array(
                "scheme" => "sqlite",
                "host" => NULL,
                "path" => "c:/full/windows/path/to/file.db",
                "dsn" => "sqlite:c:/full/windows/path/to/file.db",
                "port" => NULL,
                "user" => NULL,
                "pass" => NULL,
                "query" => NULL, 
                "fragment" => NULL,
                "unix_socket" => NULL,
                "database" => "c:/full/windows/path/to/file.db");
            $res = $manager->parseDsn($sqlitewin);
            $this->assertEqual($expectedDsn, $res);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        try {
             $expectedDsn = array(
                "scheme" => "sqlite",
                "host" => NULL,
                "path" => 'D:/full/windows/path/to/file.db',
                "dsn" => 'sqlite:D:/full/windows/path/to/file.db',
                "port" => NULL,
                "user" => NULL,
                "pass" => NULL,
                "query" => NULL, 
                "fragment" => NULL,
                "unix_socket" => NULL,
                "database" => 'D:/full/windows/path/to/file.db');
            $res = $manager->parseDsn($sqlitewin2);
            $this->assertEqual($expectedDsn, $res);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
    
    public function testCreateDatabases()
    {
        // We need to know if we're under Windows or *NIX 
        $OS = strtoupper(substr(PHP_OS, 0,3)); 

        $tmp_dir = ($OS == 'WIN') ? str_replace('\\','/',sys_get_temp_dir()) : '/tmp';
       
        $this->conn1_database = $tmp_dir . "/doctrine1.db";
        $this->conn2_database = $tmp_dir . "/doctrine2.db";

        $this->conn1 = Doctrine_Manager::connection('sqlite:///' . $this->conn1_database, 'doctrine1');
        $this->conn2 = Doctrine_Manager::connection('sqlite:///' . $this->conn2_database, 'doctrine2');
        
        $result1 = $this->conn1->createDatabase();
        $this->assertEqual($result1, 'Successfully created database for connection "doctrine1" at path "'.$this->conn1_database.'"');
        
        $result2 = $this->conn2->createDatabase();
        $this->assertEqual($result2, 'Successfully created database for connection "doctrine2" at path "'.$this->conn2_database.'"');
    }
    
    public function testDropDatabases()
    {
        $result1 = $this->conn1->dropDatabase();
        $this->assertEqual($result1, 'Successfully dropped database for connection "doctrine1" at path "'.$this->conn1_database.'"');
        
        $result2 = $this->conn2->dropDatabase();
        $this->assertEqual($result2, 'Successfully dropped database for connection "doctrine2" at path "'.$this->conn2_database.'"');
    }
    
    public function testConnectionInformationDecoded()
    {
      $dsn = 'mysql://' . urlencode('test/t') . ':' . urlencode('p@ssword') . '@localhost/' . urlencode('db/name');

      $conn = Doctrine_Manager::connection($dsn);
      $options = $conn->getOptions();

      $this->assertEqual($options['username'], 'test/t');
      $this->assertEqual($options['password'], 'p@ssword');
      $this->assertEqual($options['dsn'], 'mysql:host=localhost;dbname=db/name');
    }
    public function prepareData() { }
    public function prepareTables() { }
}
