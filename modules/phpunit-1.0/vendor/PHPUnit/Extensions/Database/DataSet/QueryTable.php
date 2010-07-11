<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: QueryTable.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.2.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

require_once 'PHPUnit/Extensions/Database/DataSet/AbstractTable.php';
require_once 'PHPUnit/Extensions/Database/DataSet/DefaultTableMetaData.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Provides the functionality to represent a database table.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2009 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.2.0
 */
class PHPUnit_Extensions_Database_DataSet_QueryTable extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new database query table object.
     *
     * @param string $table_name
     * @param string $query
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct($tableName, $query, PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        $this->query = $query;
        $this->databaseConnection = $databaseConnection;
        $this->tableName = $tableName;
    }

    /**
     * Returns the table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData()
    {
        $this->createTableMetaData();
        return parent::getTableMetaData();
    }

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount()
    {
        $this->loadData();
        return parent::getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column)
    {
        $this->loadData();
        return parent::getValue($row, $column);
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param int $row
     * @return array
     */
    public function getRow($row)
    {
        $this->loadData();
        return parent::getRow($row);
    }

    /**
     * Asserts that the given table matches this table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $other
     */
    public function assertEquals(PHPUnit_Extensions_Database_DataSet_ITable $other)
    {
        $this->loadData();
        return parent::assertEquals($other);
    }

    protected function loadData()
    {
        if ($this->data === NULL) {
            $pdoStatement = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL)
        {
            $this->loadData();
            $this->tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($this->tableName, array_keys($this->data[0]));
        }
    }
}
?>
