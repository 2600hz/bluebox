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
 * @version    SVN: $Id: XmlDataSet.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.2.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

require_once 'PHPUnit/Extensions/Database/DataSet/AbstractXmlDataSet.php';
require_once 'PHPUnit/Extensions/Database/DataSet/Persistors/Xml.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * The default implementation of a data set.
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
class PHPUnit_Extensions_Database_DataSet_XmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{

    protected function getTableInfo(Array &$tableColumns, Array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'dataset') {
            throw new Exception("The root element of an xml data set file must be called <dataset>");
        }

        foreach ($this->xmlFileContents->xpath('/dataset/table') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new Exception("Table elements must include a name attribute specifying the table name.");
            }

            $tableName = (string)$tableElement['name'];

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = array();
            }

            if (!isset($tableValues[$tableName])) {
                $tableValues[$tableName] = array();
            }

            $tableInstanceColumns = array();

            foreach ($tableElement->xpath('./column') as $columnElement) {
                $columnName = (string)$columnElement;
                if (empty($columnName)) {
                    throw new Exception("column elements cannot be empty");
                }

                if (!in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }

                $tableInstanceColumns[] = $columnName;
            }

            foreach ($tableElement->xpath('./row') as $rowElement) {
                $rowValues = array();
                $index = 0;
                foreach ($rowElement->children() as $columnValue) {
                    switch ($columnValue->getName()) {
                        case 'value':
                            $rowValues[$tableInstanceColumns[$index]] = (string)$columnValue;
                            $index++;
                            break;
                        case 'null':
                            $rowValues[$tableInstanceColumns[$index]] = NULL;
                            $index++;
                            break;
                        default:
                            throw new Exception("Unknown child in the a row element.");
                    }
                }

                $tableValues[$tableName][] = $rowValues;
            }
        }
    }

    public static function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset, $filename)
    {
        $pers = new PHPUnit_Extensions_Database_DataSet_Persistors_Xml();
        $pers->setFileName($filename);

        try {
            $pers->write($dataset);
        } catch (RuntimeException $e) {
            throw new RuntimeException(__METHOD__ . ' called with an unwritable file.');
        }
    }
}
?>
