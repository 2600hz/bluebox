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
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: SeleniumTestCase.php 4608 2009-02-03 02:51:17Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Log/Database.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Test.php';
require_once 'PHPUnit/Util/XML.php';
require_once 'PHPUnit/Extensions/SeleniumTestCase/Driver.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * TestCase class that uses Selenium to provide
 * the functionality required for web testing.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.0.0
 */
abstract class PHPUnit_Extensions_SeleniumTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var    array
     */
    public static $browsers = array();

    /**
     * @var    boolean
     */
    protected $autoStop = TRUE;

    /**
     * @var    string
     */
    protected $browserName;

    /**
     * @var    boolean
     */
    protected $collectCodeCoverageInformation = FALSE;

    /**
     * @var    string
     */
    protected $coverageScriptUrl = '';

    /**
     * @var    PHPUnit_Extensions_SeleniumTestCase_Driver[]
     */
    protected $drivers = array();

    /**
     * @var    boolean
     */
    protected $inDefaultAssertions = FALSE;

    /**
     * @var    string
     */
    protected $testId;

    /**
     * @var    array
     * @access protected
     */
    protected $verificationErrors = array();

    /**
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     * @param  array  $browser
     * @throws InvalidArgumentException
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array())
    {
        parent::__construct($name, $data, $dataName);
        $this->testId = md5(uniqid(rand(), TRUE));
        $this->getDriver($browser);
    }

    /**
     * @param  string $className
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite($className)
    {
        $suite = new PHPUnit_Framework_TestSuite;
        $suite->setName($className);

        $class            = new ReflectionClass($className);
        $classGroups      = PHPUnit_Util_Test::getGroups($class->getDocComment());
        $staticProperties = $class->getStaticProperties();

        // Create tests from Selenese/HTML files.
        if (isset($staticProperties['seleneseDirectory']) &&
            is_dir($staticProperties['seleneseDirectory'])) {
            $files = array_merge(
              self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.htm'),
              self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.html')
            );

            // Create tests from Selenese/HTML files for multiple browsers.
            if (!empty($staticProperties['browsers'])) {
                foreach ($staticProperties['browsers'] as $browser) {
                    $browserSuite = new PHPUnit_Framework_TestSuite;
                    $browserSuite->setName($className . ': ' . $browser['name']);

                    foreach ($files as $file) {
                        $browserSuite->addTest(
                          new $className($file, array(), '', $browser),
                          $classGroups
                        );
                    }

                    $suite->addTest($browserSuite);
                }
            }

            // Create tests from Selenese/HTML files for single browser.
            else {
                foreach ($files as $file) {
                    $suite->addTest(new $className($file), $classGroups);
                }
            }
        }

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties['browsers'])) {
            foreach ($staticProperties['browsers'] as $browser) {
                $browserSuite = new PHPUnit_Framework_TestSuite;
                $browserSuite->setName($className . ': ' . $browser['name']);

                foreach ($class->getMethods() as $method) {
                    if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                        $name             = $method->getName();
                        $methodDocComment = $method->getDocComment();
                        $data             = PHPUnit_Util_Test::getProvidedData($className, $name, $methodDocComment);
                        $groups           = PHPUnit_Util_Test::getGroups($methodDocComment, $classGroups);

                        // Test method with @dataProvider.
                        if (is_array($data) || $data instanceof Iterator) {
                            $dataSuite = new PHPUnit_Framework_TestSuite(
                              $className . '::' . $name
                            );

                            foreach ($data as $_dataName => $_data) {
                                $dataSuite->addTest(
                                  new $className($name, $_data, $_dataName, $browser),
                                  $groups
                                );
                            }

                            $browserSuite->addTest($dataSuite);
                        }

                        // Test method without @dataProvider.
                        else {
                            $browserSuite->addTest(
                              new $className($name, array(), '', $browser), $groups
                            );
                        }
                    }
                }

                $suite->addTest($browserSuite);
            }
        }

        // Create tests from test methods for single browser.
        else {
            foreach ($class->getMethods() as $method) {
                if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                    $name             = $method->getName();
                    $methodDocComment = $method->getDocComment();
                    $data             = PHPUnit_Util_Test::getProvidedData($className, $name, $methodDocComment);
                    $groups           = PHPUnit_Util_Test::getGroups($methodDocComment, $classGroups);

                    // Test method with @dataProvider.
                    if (is_array($data) || $data instanceof Iterator) {
                        $dataSuite = new PHPUnit_Framework_TestSuite(
                          $className . '::' . $name
                        );

                        foreach ($data as $_dataName => $_data) {
                            $dataSuite->addTest(
                              new $className($name, $_data, $_dataName),
                              $groups
                            );
                        }

                        $suite->addTest($dataSuite);
                    }

                    // Test method without @dataProvider.
                    else {
                        $suite->addTest(
                          new $className($name), $groups
                        );
                    }
                }
            }
        }

        return $suite;
    }

    /**
     * Runs the test case and collects the results in a TestResult object.
     * If no TestResult object is passed a new one will be created.
     *
     * @param  PHPUnit_Framework_TestResult $result
     * @return PHPUnit_Framework_TestResult
     * @throws InvalidArgumentException
     */
    public function run(PHPUnit_Framework_TestResult $result = NULL)
    {
        if ($result === NULL) {
            $result = $this->createResult();
        }

        $this->collectCodeCoverageInformation = $result->getCollectCodeCoverageInformation();

        foreach ($this->drivers as $driver) {
            $driver->setCollectCodeCoverageInformation(
              $this->collectCodeCoverageInformation
            );
        }

        $result->run($this);

        if ($this->collectCodeCoverageInformation) {
            $result->appendCodeCoverageInformation(
              $this, $this->getCodeCoverage()
            );
        }

        return $result;
    }

    /**
     * @param  array $browser
     * @return PHPUnit_Extensions_SeleniumTestCase_Driver
     * @since  Method available since Release 3.3.0
     */
    protected function getDriver(array $browser)
    {
        if (isset($browser['name'])) {
            if (!is_string($browser['name'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['name'] = '';
        }

        if (isset($browser['browser'])) {
            if (!is_string($browser['browser'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['browser'] = '';
        }

        if (isset($browser['host'])) {
            if (!is_string($browser['host'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['host'] = 'localhost';
        }

        if (isset($browser['port'])) {
            if (!is_int($browser['port'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['port'] = 4444;
        }

        if (isset($browser['timeout'])) {
            if (!is_int($browser['timeout'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['timeout'] = 30000;
        }

        $driver = new PHPUnit_Extensions_SeleniumTestCase_Driver;
        $driver->setName($browser['name']);
        $driver->setBrowser($browser['browser']);
        $driver->setHost($browser['host']);
        $driver->setPort($browser['port']);
        $driver->setTimeout($browser['timeout']);
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);

        $this->drivers[] = $driver;

        return $driver;
    }

    /**
     */
    protected function runTest()
    {
        $this->start();

        if (!is_file($this->name)) {
            parent::runTest();
        } else {
            $this->runSelenese($this->name);
        }

        if ($this->autoStop) {
            try {
                $this->stop();
            }

            catch (RuntimeException $e) {
            }
        }

        if (!empty($this->verificationErrors)) {
            $this->fail(implode("\n", $this->verificationErrors));
        }
    }

    /**
     * If you want to override tearDown() make sure to either call stop() or
     * parent::tearDown(). Otherwise the Selenium RC session will not be
     * closed upon test failure.
     *
     */
    protected function tearDown()
    {
        if ($this->autoStop) {
            try {
                $this->stop();
            }

            catch (RuntimeException $e) {
            }
        }
    }

    /**
     * Returns a string representation of the test case.
     *
     * @return string
     */
    public function toString()
    {
        $buffer = parent::toString();

        if (!empty($this->browserName)) {
            $buffer .= ' with browser ' . $this->browserName;
        }

        return $buffer;
    }

    /**
     * @param  boolean $autoStop
     * @throws InvalidArgumentException
     */
    public function setAutoStop($autoStop)
    {
        if (!is_bool($autoStop)) {
            throw new InvalidArgumentException;
        }

        $this->autoStop = $autoStop;
    }

    /**
     * Runs a test from a Selenese (HTML) specification.
     *
     * @param string $filename
     */
    public function runSelenese($filename)
    {
        $document = PHPUnit_Util_XML::loadFile($filename, TRUE);
        $xpath    = new DOMXPath($document);
        $rows     = $xpath->query('body/table/tbody/tr');

        foreach ($rows as $row)
        {
            $action    = NULL;
            $arguments = array();
            $columns   = $xpath->query('td', $row);

            foreach ($columns as $column)
            {
                if ($action === NULL) {
                    $action = $column->nodeValue;
                } else {
                    $arguments[] = $column->nodeValue;
                }
            }

            if (method_exists($this, $action)) {
                call_user_func_array(array($this, $action), $arguments);
            } else {
                $this->__call($action, $arguments);
            }
        }
    }

    /**
     * Delegate method calls to the driver.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return mixed
     * @method unknown  addLocationStrategy()
     * @method unknown  addSelection()
     * @method unknown  addSelectionAndWait()
     * @method unknown  allowNativeXpath()
     * @method unknown  altKeyDown()
     * @method unknown  altKeyDownAndWait()
     * @method unknown  altKeyUp()
     * @method unknown  altKeyUpAndWait()
     * @method unknown  answerOnNextPrompt()
     * @method unknown  assignId()
     * @method unknown  break()
     * @method unknown  captureEntirePageScreenshot()
     * @method unknown  captureScreenshot()
     * @method unknown  check()
     * @method unknown  chooseCancelOnNextConfirmation()
     * @method unknown  chooseOkOnNextConfirmation()
     * @method unknown  click()
     * @method unknown  clickAndWait()
     * @method unknown  clickAt()
     * @method unknown  clickAtAndWait()
     * @method unknown  close()
     * @method unknown  contextMenu()
     * @method unknown  contextMenuAndWait()
     * @method unknown  contextMenuAt()
     * @method unknown  contextMenuAtAndWait()
     * @method unknown  controlKeyDown()
     * @method unknown  controlKeyDownAndWait()
     * @method unknown  controlKeyUp()
     * @method unknown  controlKeyUpAndWait()
     * @method unknown  createCookie()
     * @method unknown  createCookieAndWait()
     * @method unknown  deleteAllVisibleCookies()
     * @method unknown  deleteAllVisibleCookiesAndWait()
     * @method unknown  deleteCookie()
     * @method unknown  deleteCookieAndWait()
     * @method unknown  doubleClick()
     * @method unknown  doubleClickAndWait()
     * @method unknown  doubleClickAt()
     * @method unknown  doubleClickAtAndWait()
     * @method unknown  dragAndDrop()
     * @method unknown  dragAndDropAndWait()
     * @method unknown  dragAndDropToObject()
     * @method unknown  dragAndDropToObjectAndWait()
     * @method unknown  dragDrop()
     * @method unknown  dragDropAndWait()
     * @method unknown  echo()
     * @method unknown  fireEvent()
     * @method unknown  fireEventAndWait()
     * @method unknown  focus()
     * @method string   getAlert()
     * @method array    getAllButtons()
     * @method array    getAllFields()
     * @method array    getAllLinks()
     * @method array    getAllWindowIds()
     * @method array    getAllWindowNames()
     * @method array    getAllWindowTitles()
     * @method string   getAttribute()
     * @method array    getAttributeFromAllWindows()
     * @method string   getBodyText()
     * @method string   getConfirmation()
     * @method string   getCookie()
     * @method integer  getCursorPosition()
     * @method integer  getElementHeight()
     * @method integer  getElementIndex()
     * @method integer  getElementPositionLeft()
     * @method integer  getElementPositionTop()
     * @method integer  getElementWidth()
     * @method string   getEval()
     * @method string   getExpression()
     * @method string   getHtmlSource()
     * @method string   getLocation()
     * @method string   getLogMessages()
     * @method integer  getMouseSpeed()
     * @method string   getPrompt()
     * @method array    getSelectOptions()
     * @method string   getSelectedId()
     * @method array    getSelectedIds()
     * @method string   getSelectedIndex()
     * @method array    getSelectedIndexes()
     * @method string   getSelectedLabel()
     * @method array    getSelectedLabels()
     * @method string   getSelectedValue()
     * @method array    getSelectedValues()
     * @method unknown  getSpeed()
     * @method unknown  getSpeedAndWait()
     * @method string   getTable()
     * @method string   getText()
     * @method string   getTitle()
     * @method string   getValue()
     * @method boolean  getWhetherThisFrameMatchFrameExpression()
     * @method boolean  getWhetherThisWindowMatchWindowExpression()
     * @method integer  getXpathCount()
     * @method unknown  goBack()
     * @method unknown  goBackAndWait()
     * @method unknown  highlight()
     * @method unknown  highlightAndWait()
     * @method unknown  ignoreAttributesWithoutValue()
     * @method boolean  isAlertPresent()
     * @method boolean  isChecked()
     * @method boolean  isConfirmationPresent()
     * @method boolean  isEditable()
     * @method boolean  isElementPresent()
     * @method boolean  isOrdered()
     * @method boolean  isPromptPresent()
     * @method boolean  isSomethingSelected()
     * @method boolean  isTextPresent()
     * @method boolean  isVisible()
     * @method unknown  keyDown()
     * @method unknown  keyDownAndWait()
     * @method unknown  keyPress()
     * @method unknown  keyPressAndWait()
     * @method unknown  keyUp()
     * @method unknown  keyUpAndWait()
     * @method unknown  metaKeyDown()
     * @method unknown  metaKeyDownAndWait()
     * @method unknown  metaKeyUp()
     * @method unknown  metaKeyUpAndWait()
     * @method unknown  mouseDown()
     * @method unknown  mouseDownAndWait()
     * @method unknown  mouseDownAt()
     * @method unknown  mouseDownAtAndWait()
     * @method unknown  mouseMove()
     * @method unknown  mouseMoveAndWait()
     * @method unknown  mouseMoveAt()
     * @method unknown  mouseMoveAtAndWait()
     * @method unknown  mouseOut()
     * @method unknown  mouseOutAndWait()
     * @method unknown  mouseOver()
     * @method unknown  mouseOverAndWait()
     * @method unknown  mouseUp()
     * @method unknown  mouseUpAndWait()
     * @method unknown  mouseUpAt()
     * @method unknown  mouseUpAtAndWait()
     * @method unknown  mouseUpRight()
     * @method unknown  mouseUpRightAndWait()
     * @method unknown  mouseUpRightAt()
     * @method unknown  mouseUpRightAtAndWait()
     * @method unknown  open()
     * @method unknown  openWindow()
     * @method unknown  openWindowAndWait()
     * @method unknown  pause()
     * @method unknown  refresh()
     * @method unknown  refreshAndWait()
     * @method unknown  removeAllSelections()
     * @method unknown  removeAllSelectionsAndWait()
     * @method unknown  removeSelection()
     * @method unknown  removeSelectionAndWait()
     * @method unknown  runScript()
     * @method unknown  select()
     * @method unknown  selectAndWait()
     * @method unknown  selectFrame()
     * @method unknown  selectWindow()
     * @method unknown  setBrowserLogLevel()
     * @method unknown  setContext()
     * @method unknown  setCursorPosition()
     * @method unknown  setCursorPositionAndWait()
     * @method unknown  setMouseSpeed()
     * @method unknown  setMouseSpeedAndWait()
     * @method unknown  setSpeed()
     * @method unknown  setSpeedAndWait()
     * @method unknown  shiftKeyDown()
     * @method unknown  shiftKeyDownAndWait()
     * @method unknown  shiftKeyUp()
     * @method unknown  shiftKeyUpAndWait()
     * @method unknown  store()
     * @method unknown  storeAlert()
     * @method unknown  storeAlertPresent()
     * @method unknown  storeAllButtons()
     * @method unknown  storeAllFields()
     * @method unknown  storeAllLinks()
     * @method unknown  storeAllWindowIds()
     * @method unknown  storeAllWindowNames()
     * @method unknown  storeAllWindowTitle()s
     * @method unknown  storeAttribute()
     * @method unknown  storeAttributeFromAllWindows()
     * @method unknown  storeBodyText()
     * @method unknown  storeChecked()
     * @method unknown  storeConfirmation()
     * @method unknown  storeConfirmationPresent()
     * @method unknown  storeCookie()
     * @method unknown  storeCookieByName()
     * @method unknown  storeCookiePresent()
     * @method unknown  storeCursorPosition()
     * @method unknown  storeEditable()
     * @method unknown  storeElementHeight()
     * @method unknown  storeElementIndex()
     * @method unknown  storeElementPositionLeft()
     * @method unknown  storeElementPositionTop()
     * @method unknown  storeElementPresent()
     * @method unknown  storeElementWidth()
     * @method unknown  storeEval()
     * @method unknown  storeExpression()
     * @method unknown  storeHtmlSource()
     * @method unknown  storeLocation()
     * @method unknown  storeMouseSpeed()
     * @method unknown  storeOrdered()
     * @method unknown  storePrompt()
     * @method unknown  storePromptPresent()
     * @method unknown  storeSelectOptions()
     * @method unknown  storeSelectedId()
     * @method unknown  storeSelectedIds()
     * @method unknown  storeSelectedIndex()
     * @method unknown  storeSelectedIndexes()
     * @method unknown  storeSelectedLabel()
     * @method unknown  storeSelectedLabels()
     * @method unknown  storeSelectedValue()
     * @method unknown  storeSelectedValues()
     * @method unknown  storeSomethingSelected()
     * @method unknown  storeSpeed()
     * @method unknown  storeTable()
     * @method unknown  storeText()
     * @method unknown  storeTextPresent()
     * @method unknown  storeTitle()
     * @method unknown  storeValue()
     * @method unknown  storeVisible()
     * @method unknown  storeWhetherThisFrameMatchFrameExpression()
     * @method unknown  storeWhetherThisWindowMatchWindowExpression()
     * @method unknown  storeXpathCount()
     * @method unknown  submit()
     * @method unknown  submitAndWait()
     * @method unknown  type()
     * @method unknown  typeAndWait()
     * @method unknown  typeKeys()
     * @method unknown  typeKeysAndWait()
     * @method unknown  uncheck()
     * @method unknown  uncheckAndWait()
     * @method unknown  waitForCondition()
     * @method unknown  waitForPageToLoad()
     * @method unknown  waitForPopUp()
     * @method unknown  windowFocus()
     * @method unknown  windowMaximize()
     */
    public function __call($command, $arguments)
    {
        return call_user_func_array(
          array($this->drivers[0], $command), $arguments
        );
    }

    /**
     * Asserts that an alert is present.
     *
     * @param  string $message
     */
    public function assertAlertPresent($message = 'No alert present.')
    {
        $this->assertTrue($this->isAlertPresent(), $message);
    }

    /**
     * Asserts that no alert is present.
     *
     * @param  string $message
     */
    public function assertNoAlertPresent($message = 'Alert present.')
    {
        $this->assertFalse($this->isAlertPresent(), $message);
    }

    /**
     * Asserts that an option is checked.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertChecked($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" not checked.',
              $locator
            );
        }

        $this->assertTrue($this->isChecked($locator), $message);
    }

    /**
     * Asserts that an option is not checked.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertNotChecked($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" checked.',
              $locator
            );
        }

        $this->assertFalse($this->isChecked($locator), $message);
    }

    /**
     * Assert that a confirmation is present.
     *
     * @param  string $message
     */
    public function assertConfirmationPresent($message = 'No confirmation present.')
    {
        $this->assertTrue($this->isConfirmationPresent(), $message);
    }

    /**
     * Assert that no confirmation is present.
     *
     * @param  string $message
     */
    public function assertNoConfirmationPresent($message = 'Confirmation present.')
    {
        $this->assertFalse($this->isConfirmationPresent(), $message);
    }

    /**
     * Asserts that an input field is editable.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertEditable($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" not editable.',
              $locator
            );
        }

        $this->assertTrue($this->isEditable($locator), $message);
    }

    /**
     * Asserts that an input field is not editable.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertNotEditable($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" editable.',
              $locator
            );
        }

        $this->assertFalse($this->isEditable($locator), $message);
    }

    /**
     * Asserts that an element's value is equal to a given string.
     *
     * @param  string $locator
     * @param  string $text
     * @param  string $message
     */
    public function assertElementValueEquals($locator, $text, $message = '')
    {
        $this->assertEquals($text, $this->getValue($locator), $message);
    }

    /**
     * Asserts that an element's value is not equal to a given string.
     *
     * @param  string $locator
     * @param  string $text
     * @param  string $message
     */
    public function assertElementValueNotEquals($locator, $text, $message = '')
    {
        $this->assertNotEquals($text, $this->getValue($locator), $message);
    }

    /**
     * Asserts that an element contains a given string.
     *
     * @param  string $locator
     * @param  string $text
     * @param  string $message
     */
    public function assertElementContainsText($locator, $text, $message = '')
    {
        $this->assertContains($text, $this->getValue($locator), $message);
    }

    /**
     * Asserts that an element does not contain a given string.
     *
     * @param  string $locator
     * @param  string $text
     * @param  string $message
     */
    public function assertElementNotContainsText($locator, $text, $message = '')
    {
        $this->assertNotContains($text, $this->getValue($locator), $message);
    }

    /**
     * Asserts than an element is present.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertElementPresent($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Element "%s" not present.',
              $locator
            );
        }

        $this->assertTrue($this->isElementPresent($locator), $message);
    }

    /**
     * Asserts than an element is not present.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertElementNotPresent($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Element "%s" present.',
              $locator
            );
        }

        $this->assertFalse($this->isElementPresent($locator), $message);
    }

    /**
     * Asserts that the location is equal to a specified one.
     *
     * @param  string $location
     * @param  string $message
     */
    public function assertLocationEquals($location, $message = '')
    {
        $this->assertEquals($location, $this->getLocation(), $message);
    }

    /**
     * Asserts that the location is not equal to a specified one.
     *
     * @param  string $location
     * @param  string $message
     */
    public function assertLocationNotEquals($location, $message = '')
    {
        $this->assertNotEquals($location, $this->getLocation(), $message);
    }

    /**
     * Asserts than a prompt is present.
     *
     * @param  string $message
     */
    public function assertPromptPresent($message = 'No prompt present.')
    {
        $this->assertTrue($this->isPromptPresent(), $message);
    }

    /**
     * Asserts than no prompt is present.
     *
     * @param  string $message
     */
    public function assertNoPromptPresent($message = 'Prompt present.')
    {
        $this->assertFalse($this->isPromptPresent(), $message);
    }

    /**
     * Asserts that a select element has a specific option.
     *
     * @param  string $selectLocator
     * @param  string $option
     * @param  string $message
     * @since  Method available since Release 3.2.0
     */
    public function assertSelectHasOption($selectLocator, $option, $message = '')
    {
        $this->assertContains($option, $this->getSelectOptions($selectLocator), $message);
    }

    /**
     * Asserts that a select element does not have a specific option.
     *
     * @param  string $selectLocator
     * @param  string $option
     * @param  string $message
     * @since  Method available since Release 3.2.0
     */
    public function assertSelectNotHasOption($selectLocator, $option, $message = '')
    {
        $this->assertNotContains($option, $this->getSelectOptions($selectLocator), $message);
    }

    /**
     * Asserts that a specific label is selected.
     *
     * @param  string $selectLocator
     * @param  string $value
     * @param  string $message
     * @since  Method available since Release 3.2.0
     */
    public function assertSelected($selectLocator, $option, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Label "%s" not selected in "%s".',
              $option,
              $selectLocator
            );
        }

        $this->assertEquals(
          $option,
          $this->getSelectedLabel($selectLocator),
          $message
        );
    }

    /**
     * Asserts that a specific label is not selected.
     *
     * @param  string $selectLocator
     * @param  string $value
     * @param  string $message
     * @since  Method available since Release 3.2.0
     */
    public function assertNotSelected($selectLocator, $option, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Label "%s" selected in "%s".',
              $option,
              $selectLocator
            );
        }

        $this->assertNotEquals(
          $option,
          $this->getSelectedLabel($selectLocator),
          $message
        );
    }

    /**
     * Asserts that a specific value is selected.
     *
     * @param  string $selectLocator
     * @param  string $value
     * @param  string $message
     */
    public function assertIsSelected($selectLocator, $value, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Value "%s" not selected in "%s".',
              $value,
              $selectLocator
            );
        }

        $this->assertEquals(
          $value, $this->getSelectedValue($selectLocator),
          $message
        );
    }

    /**
     * Asserts that a specific value is not selected.
     *
     * @param  string $selectLocator
     * @param  string $value
     * @param  string $message
     */
    public function assertIsNotSelected($selectLocator, $value, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Value "%s" selected in "%s".',
              $value,
              $selectLocator
            );
        }

        $this->assertNotEquals(
          $value,
          $this->getSelectedValue($selectLocator),
          $message
        );
    }

    /**
     * Asserts that something is selected.
     *
     * @param  string $selectLocator
     * @param  string $message
     */
    public function assertSomethingSelected($selectLocator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Nothing selected from "%s".',
              $selectLocator
            );
        }

        $this->assertTrue($this->isSomethingSelected($selectLocator), $message);
    }

    /**
     * Asserts that nothing is selected.
     *
     * @param  string $selectLocator
     * @param  string $message
     */
    public function assertNothingSelected($selectLocator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              'Something selected from "%s".',
              $selectLocator
            );
        }

        $this->assertFalse($this->isSomethingSelected($selectLocator), $message);
    }

    /**
     * Asserts that a given text is present.
     *
     * @param  string $pattern
     * @param  string $message
     */
    public function assertTextPresent($pattern, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" not present.',
              $pattern
            );
        }

        $this->assertTrue($this->isTextPresent($pattern), $message);
    }

    /**
     * Asserts that a given text is not present.
     *
     * @param  string $pattern
     * @param  string $message
     */
    public function assertTextNotPresent($pattern, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" present.',
              $pattern
            );
        }

        $this->assertFalse($this->isTextPresent($pattern), $message);
    }

    /**
     * Asserts that the title is equal to a given string.
     *
     * @param  string $title
     * @param  string $message
     */
    public function assertTitleEquals($title, $message = '')
    {
        $this->assertEquals($title, $this->getTitle(), $message);
    }

    /**
     * Asserts that the title is not equal to a given string.
     *
     * @param  string $title
     * @param  string $message
     */
    public function assertTitleNotEquals($title, $message = '')
    {
        $this->assertNotEquals($title, $this->getTitle(), $message);
    }

    /**
     * Asserts that something is visible.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertVisible($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" not visible.',
              $locator
            );
        }

        $this->assertTrue($this->isVisible($locator), $message);
    }

    /**
     * Asserts that something is not visible.
     *
     * @param  string $locator
     * @param  string $message
     */
    public function assertNotVisible($locator, $message = '')
    {
        if ($message == '') {
            $message = sprintf(
              '"%s" visible.',
              $locator
            );
        }

        $this->assertFalse($this->isVisible($locator), $message);
    }

    /**
     * Template Method that is called after Selenium actions.
     *
     * @param  string $action
     * @since  Method available since Release 3.1.0
     */
    protected function defaultAssertions($action)
    {
    }

    /**
     * @return array
     * @since  Method available since Release 3.2.0
     */
    protected function getCodeCoverage()
    {
        if (!empty($this->coverageScriptUrl)) {
            $url = sprintf(
              '%s?PHPUNIT_SELENIUM_TEST_ID=%s',
              $this->coverageScriptUrl,
              $this->testId
            );

            $buffer = @file_get_contents($url);

            if ($buffer !== FALSE) {
                return $this->matchLocalAndRemotePaths(unserialize($buffer));
            }
        }

        return array();
    }

    /**
     * @param  array $coverage
     * @return array
     * @author Mattis Stordalen Flister <mattis@xait.no>
     * @since  Method available since Release 3.2.9
     */
    protected function matchLocalAndRemotePaths(array $coverage)
    {
        $coverageWithLocalPaths = array();

        foreach ($coverage as $originalRemotePath => $data) {
            $remotePath = $originalRemotePath;
            $separator  = $this->findDirectorySeparator($remotePath);

            while (!($localpath = PHPUnit_Util_Filesystem::fileExistsInIncludePath($remotePath)) &&
                   strpos($remotePath, $separator) !== FALSE) {
                $remotePath = substr($remotePath, strpos($remotePath, $separator) + 1);
            }

            if ($localpath && md5_file($localpath) == $data['md5']) {
                $coverageWithLocalPaths[$localpath] = $data['coverage'];
            }
        }

        return $coverageWithLocalPaths;
    }

    /**
     * @param  string $path
     * @return string
     * @author Mattis Stordalen Flister <mattis@xait.no>
     * @since  Method available since Release 3.2.9
     */
    protected function findDirectorySeparator($path)
    {
        if (strpos($path, '/') !== FALSE) {
            return '/';
        }

        return '\\';
    }

    /**
     * @param  string $path
     * @return array
     * @author Mattis Stordalen Flister <mattis@xait.no>
     * @since  Method available since Release 3.2.9
     */
    protected function explodeDirectories($path)
    {
        return explode($this->findDirectorySeparator($path), dirname($path));
    }

    /**
     * @param  string $directory
     * @param  string $suffix
     * @return array
     * @since  Method available since Release 3.3.0
     */
    protected static function getSeleneseFiles($directory, $suffix)
    {
        $files = array();

        $iterator = new PHPUnit_Util_FilterIterator(
          new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
          ),
          $suffix
        );

        foreach ($iterator as $file) {
            $files[] = (string)$file;
        }

        return $files;
    }

    /**
     * @param  string $action
     * @since  Method available since Release 3.2.0
     */
    public function runDefaultAssertions($action)
    {
        if (!$this->inDefaultAssertions) {
            $this->inDefaultAssertions = TRUE;
            $this->defaultAssertions($action);
            $this->inDefaultAssertions = FALSE;
        }
    }
}
?>
