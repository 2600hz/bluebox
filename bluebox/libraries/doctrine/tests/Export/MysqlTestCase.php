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
 * Doctrine_Export_Mysql_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Export_Mysql_TestCase extends Doctrine_UnitTestCase 
{
    public function prepareTables() 
    { }
    public function prepareData() 
    { }

    public function testAlterTableThrowsExceptionWithoutValidTableName()
    {
        try {
            $this->export->alterTable(0, array(), array());

            $this->fail();
        } catch(Doctrine_Export_Exception $e) {
            $this->pass();
        }
    }
    public function testCreateTableExecutesSql() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1));
        $options = array('type' => 'MYISAM');
        
        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id INT UNSIGNED) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsDefaultTableType() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1));

        $this->export->createTable($name, $fields);

        // INNODB is the default type
        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id INT UNSIGNED) ENGINE = INNODB');
    }
    public function testCreateTableSupportsMultiplePks() 
    {
        $name = 'mytable';
        $fields  = array('name' => array('type' => 'char', 'length' => 10),
                         'type' => array('type' => 'integer', 'length' => 3));
                         
        $options = array('primary' => array('name', 'type'));
        $this->export->createTable($name, $fields, $options);
        
        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (name CHAR(10), type MEDIUMINT, PRIMARY KEY(name, type)) ENGINE = INNODB');
    }
    public function testCreateTableSupportsAutoincPks() 
    {
        $name = 'mytable';

        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1, 'autoincrement' => true));
        $options = array('primary' => array('id'), 
                        'type' => 'INNODB');
        
        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id INT UNSIGNED AUTO_INCREMENT, PRIMARY KEY(id)) ENGINE = INNODB');
    }
    public function testCreateTableSupportsCharType() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'char', 'length' => 3));
        $options = array('type' => 'MYISAM');
        
        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id CHAR(3)) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsCharType2() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'char'));
        $options = array('type' => 'MYISAM');
        
        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id CHAR(255)) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsVarcharType() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'varchar', 'length' => '100'));
        $options = array('type' => 'MYISAM');

        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id VARCHAR(100)) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsIntegerType() 
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'integer', 'length' => '10'));
        $options = array('type' => 'MYISAM');

        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id BIGINT) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsBlobType() 
    {
        $name = 'mytable';
        
        $fields  = array('content' => array('type' => 'blob'));
        $options = array('type' => 'MYISAM');

        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (content LONGBLOB) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsBlobType2() 
    {
        $name = 'mytable';
        
        $fields  = array('content' => array('type' => 'blob', 'length' => 2000));
        $options = array('type' => 'MYISAM');

        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (content BLOB) ENGINE = MYISAM');
    }

    public function testCreateTableSupportsBooleanType()
    {
        $name = 'mytable';
        
        $fields  = array('id' => array('type' => 'boolean'));
        $options = array('type' => 'MYISAM');

        $this->export->createTable($name, $fields, $options);

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE mytable (id TINYINT(1)) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsForeignKeys()
    {
        $name = 'mytable';
        
        $fields = array('id' => array('type' => 'boolean', 'primary' => true),
                        'foreignKey' => array('type' => 'integer')
                        );
        $options = array('type' => 'INNODB',
                         'foreignKeys' => array(array('local' => 'foreignKey',
                                                      'foreign' => 'id',
                                                      'foreignTable' => 'sometable'))
                         );


        $sql = $this->export->createTableSql($name, $fields, $options);

        $this->assertEqual($sql[0], 'CREATE TABLE mytable (id TINYINT(1), foreignKey INT, INDEX foreignKey_idx (foreignKey)) ENGINE = INNODB');
        $this->assertEqual($sql[1], 'ALTER TABLE mytable ADD FOREIGN KEY (foreignKey) REFERENCES sometable(id)');
    }
    public function testForeignKeyIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);

        $name = 'mytable';

        $fields = array('id' => array('type' => 'boolean', 'primary' => true),
                        'foreignKey' => array('type' => 'integer')
                        );
        $options = array('type' => 'INNODB',
                         'foreignKeys' => array(array('local' => 'foreignKey',
                                                      'foreign' => 'id',
                                                      'foreignTable' => 'sometable'))
                         );


        $sql = $this->export->createTableSql($name, $fields, $options);

        $this->assertEqual($sql[0], 'CREATE TABLE `mytable` (`id` TINYINT(1), `foreignKey` INT, INDEX `foreignKey_idx` (`foreignKey`)) ENGINE = INNODB');
        $this->assertEqual($sql[1], 'ALTER TABLE `mytable` ADD FOREIGN KEY (`foreignKey`) REFERENCES `sometable`(`id`)');

        $this->conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, false);
    }
    public function testIndexIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);

        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1, 'autoincrement' => true, 'unique' => true),
                         'name' => array('type' => 'string', 'length' => 4),
                         );

        $options = array('primary' => array('id'),
                         'indexes' => array('myindex' => array('fields' => array('id', 'name')))
                         );

        $this->export->createTable('sometable', $fields, $options);

        //this was the old line, but it looks like the table is created first 
        //and then the index so i replaced it with the ones below
        //$this->assertEqual($var, 'CREATE TABLE sometable (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(4), INDEX myindex (id, name))');

        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE `sometable` (`id` INT UNSIGNED AUTO_INCREMENT, `name` VARCHAR(4), INDEX `myindex_idx` (`id`, `name`), PRIMARY KEY(`id`)) ENGINE = INNODB');

        $this->conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, false);
    }
    public function testCreateTableDoesNotAutoAddIndexesWhenIndexForFkFieldAlreadyExists()
    {
        $name = 'mytable';
        
        $fields = array('id' => array('type' => 'boolean', 'primary' => true),
                        'foreignKey' => array('type' => 'integer')
                        );
        $options = array('type' => 'INNODB',
                         'foreignKeys' => array(array('local' => 'foreignKey',
                                                      'foreign' => 'id',
                                                      'foreignTable' => 'sometable')),
                         'indexes' => array('myindex' => array('fields' => array('foreignKey'))),
                         );


        $sql = $this->export->createTableSql($name, $fields, $options);
        $this->assertEqual($sql[0], 'CREATE TABLE mytable (id TINYINT(1), foreignKey INT, INDEX myindex_idx (foreignKey)) ENGINE = INNODB');
        $this->assertEqual($sql[1], 'ALTER TABLE mytable ADD FOREIGN KEY (foreignKey) REFERENCES sometable(id)');
    }
    public function testCreateDatabaseExecutesSql()
    {
        $this->export->createDatabase('db');

        $this->assertEqual($this->adapter->pop(), 'CREATE DATABASE db');
    }
    public function testDropDatabaseExecutesSql() 
    {
        $this->export->dropDatabase('db');

        $this->assertEqual($this->adapter->pop(), 'DROP DATABASE db');
    }

    public function testDropIndexExecutesSql() 
    {
        $this->export->dropIndex('sometable', 'relevancy');

        $this->assertEqual($this->adapter->pop(), 'DROP INDEX relevancy_idx ON sometable');
    }
    public function testUnknownIndexSortingAttributeThrowsException()
    {
        $fields = array('id' => array('sorting' => 'ASC'),
                        'name' => array('sorting' => 'unknown'));

        try {
            $this->export->getIndexFieldDeclarationList($fields);
            $this->fail();
        } catch(Doctrine_Export_Exception $e) {
            $this->pass();
        }
    }
    public function testIndexDeclarationsSupportSortingAndLengthAttributes()
    {
        $fields = array('id' => array('sorting' => 'ASC', 'length' => 10),
                        'name' => array('sorting' => 'DESC', 'length' => 1));

        $this->assertEqual($this->export->getIndexFieldDeclarationList($fields), 'id(10) ASC, name(1) DESC');
    }
    public function testCreateTableSupportsIndexesUsingSingleFieldString()
    {
        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1, 'autoincrement' => true, 'unique' => true),
                         'name' => array('type' => 'string', 'length' => 4),
                         );

        $options = array('primary' => array('id'),
                         'indexes' => array('myindex' => array(
                                                    'fields' => 'name'))
                         );

        $this->export->createTable('sometable', $fields, $options);        
        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE sometable (id INT UNSIGNED AUTO_INCREMENT, name VARCHAR(4), INDEX myindex_idx (name), PRIMARY KEY(id)) ENGINE = INNODB');
    }
    public function testCreateTableSupportsIndexesWithCustomSorting()
    {
        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1, 'autoincrement' => true, 'unique' => true),
                         'name' => array('type' => 'string', 'length' => 4),
                         );

        $options = array('primary' => array('id'),
                         'indexes' => array('myindex' => array(
                                                    'fields' => array(
                                                            'id' => array('sorting' => 'ASC'), 
                                                            'name' => array('sorting' => 'DESC')
                                                                )
                                                            ))
                         );

        $this->export->createTable('sometable', $fields, $options);
        
        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE sometable (id INT UNSIGNED AUTO_INCREMENT, name VARCHAR(4), INDEX myindex_idx (id ASC, name DESC), PRIMARY KEY(id)) ENGINE = INNODB');
    }
    public function testCreateTableSupportsFulltextIndexes()
    {
        $fields  = array('id' => array('type' => 'integer', 'unsigned' => 1, 'autoincrement' => true, 'unique' => true),
                         'content' => array('type' => 'string', 'length' => 4),
                         );

        $options = array('primary' => array('id'),
                         'indexes' => array('myindex' => array(
                                                    'fields' => array(
                                                            'content' => array('sorting' => 'DESC')
                                                                ),
                                                    'type' => 'fulltext',
                                                            )),
                         'type'    => 'MYISAM',
                         );

        $this->export->createTable('sometable', $fields, $options);
        
        $this->assertEqual($this->adapter->pop(), 'CREATE TABLE sometable (id INT UNSIGNED AUTO_INCREMENT, content VARCHAR(4), FULLTEXT INDEX myindex_idx (content DESC), PRIMARY KEY(id)) ENGINE = MYISAM');
    }
    public function testCreateTableSupportsCompoundForeignKeys()
    {
        $name = 'mytable';

        $fields = array('id' => array('type' => 'boolean', 'primary' => true),
                        'lang' => array('type' => 'integer', 'primary' => true)
                        );
        $options = array('type' => 'INNODB',
                         'foreignKeys' => array(array('local' => array ('id', 'lang' ),
                                                      'foreign' => array ('id', 'lang'),
                                                      'foreignTable' => 'sometable'))
                         );

        $sql = $this->export->createTableSql($name, $fields, $options);

        $this->assertEqual($sql[0], 'CREATE TABLE mytable (id TINYINT(1), lang INT, INDEX id_idx (id), INDEX lang_idx (lang)) ENGINE = INNODB');
        $this->assertEqual($sql[1], 'ALTER TABLE mytable ADD FOREIGN KEY (id, lang) REFERENCES sometable(id, lang)');
    }
}