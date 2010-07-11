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
 * @version    SVN: $Id: CompositeDataSetTest.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.2.0
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/DefaultDataSet.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/DefaultTable.php';
require_once 'PHPUnit/Extensions/Database/DataSet/DefaultTableMetaData.php';
require_once 'PHPUnit/Extensions/Database/DataSet/CompositeDataSet.php';

/**
 * @category   Testing
 * @package    PHPUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: CompositeDataSetTest.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.3.0
 */
class Extensions_Database_DataSet_CompositeDataSetTest extends PHPUnit_Framework_TestCase
{
    protected $expectedDataSet1;
    protected $expectedDataSet2;
    protected $expectedDataSet3;

    public function setUp()
    {
        $table1MetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', array('table1_id', 'column1', 'column2', 'column3', 'column4')
        );
        $table2MetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table2', array('table2_id', 'column5', 'column6', 'column7', 'column8')
        );

        $table3MetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table3', array('table3_id', 'column9', 'column10', 'column11', 'column12')
        );

        $table1 = new PHPUnit_Extensions_Database_DataSet_DefaultTable($table1MetaData);
        $table2 = new PHPUnit_Extensions_Database_DataSet_DefaultTable($table2MetaData);
        $table3 = new PHPUnit_Extensions_Database_DataSet_DefaultTable($table3MetaData);

        $table1->addRow(array(
            'table1_id' => 1,
            'column1' => 'tgfahgasdf',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;'
        ));
        $table1->addRow(array(
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads'
        ));
        $table1->addRow(array(
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => 1654.4,
            'column4' => 'asfgklg'
        ));

        $table2->addRow(array(
            'table2_id' => 1,
            'column5' => 'fhah',
            'column6' => 456,
            'column7' => 46.5,
            'column8' => 'fsdb, ghfdas'
        ));
        $table2->addRow(array(
            'table2_id' => 2,
            'column5' => 'asdhfoih',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => '43asd "fhgj" sfadh'
        ));
        $table2->addRow(array(
            'table2_id' => 3,
            'column5' => 'ajsdlkfguitah',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => 'thesethasdl
asdflkjsadf asdfsadfhl "adsf, halsdf" sadfhlasdf'
        ));

        $table3->addRow(array(
            'table3_id' => 1,
            'column9' => 'sfgsda',
            'column10' => 16,
            'column11' => 45.57,
            'column12' => 'sdfh .ds,ajfas asdf h'
        ));
        $table3->addRow(array(
            'table3_id' => 2,
            'column9' => 'afdstgb',
            'column10' => 41,
            'column11' => 46.645,
            'column12' => '87yhasdf sadf yah;/a '
        ));
        $table3->addRow(array(
            'table3_id' => 3,
            'column9' => 'gldsf',
            'column10' => 46,
            'column11' => 123.456,
            'column12' => '0y8hosnd a/df7y olgbjs da'
        ));

        $this->expectedDataSet1 = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet(array($table1, $table2));
        $this->expectedDataSet2 = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet(array($table3));
        $this->expectedDataSet3 = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet(array($table1, $table2, $table3));
    }

    public function testCompositeDataSet()
    {
        $actual = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array($this->expectedDataSet1, $this->expectedDataSet2));

        PHPUnit_Extensions_Database_TestCase::assertDataSetsEqual($this->expectedDataSet3, $actual);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDuplicateTables()
    {
        new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array($this->expectedDataSet1, $this->expectedDataSet1));
    }
}
?>
