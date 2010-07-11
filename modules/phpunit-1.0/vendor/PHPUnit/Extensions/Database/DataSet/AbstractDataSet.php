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
 * @version    SVN: $Id: AbstractDataSet.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.2.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

require_once 'PHPUnit/Extensions/Database/DataSet/IDataSet.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Implements the basic functionality of data sets.
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
abstract class PHPUnit_Extensions_Database_DataSet_AbstractDataSet implements PHPUnit_Extensions_Database_DataSet_IDataSet
{

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected abstract function createIterator($reverse = FALSE);

    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames()
    {
        $tableNames = array();

        foreach ($this->getIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            $tableNames[] = $table->getTableMetaData()->getTableName();
        }

        return $tableNames;
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData($tableName)
    {
        return $this->getTable($tableName)->getTableMetaData();
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITable
     */
    public function getTable($tableName)
    {
        foreach ($this->getIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            if ($table->getTableMetaData()->getTableName() == $tableName) {
                return $table;
            }
        }
    }

    /**
     * Returns an iterator for all table objects in the given dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    public function getIterator()
    {
        return $this->createIterator();
    }

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    public function getReverseIterator()
    {
        return $this->createIterator(TRUE);
    }

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $other
     */
    public function assertEquals(PHPUnit_Extensions_Database_DataSet_IDataSet $other)
    {
        $thisTableNames = $this->getTableNames();
        $otherTableNames = $other->getTableNames();

        sort($thisTableNames);
        sort($otherTableNames);

        if ($thisTableNames != $otherTableNames) {
            throw new Exception("Expected following tables: " . implode(', ', $thisTableNames) . "; has columns: " . implode(', ', $otherTableNames));
        }

        foreach ($thisTableNames as $tableName) {
            $this->getTable($tableName)->assertEquals($other->getTable($tableName));
        }

        return TRUE;
    }

    public function __toString()
    {
        $iterator = $this->getIterator();

        $dataSetString = '';
        foreach ($iterator as $table) {
            $dataSetString .= $table->__toString();
        }

        return $dataSetString;
    }
}
?>
