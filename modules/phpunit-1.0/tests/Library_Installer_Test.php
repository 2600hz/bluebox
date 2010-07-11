<?php
/**
 * Installer Library Unit Tests
 *
 * @package	Core
 * @subpackage	Helpers
 * @author	K Anderson
 * @group	core
 * @group	core.libraries
 * @group	core.libraries.installer
 */
class Library_Installer_Test extends PHPUnit_Framework_TestCase
{
    public $validActions = array( 'downgrade', 'install', 'upgrade', 'enable', 'disable', 'uninstall', 'repair', 'verify' );
   
	/**
	 * Tests the Bluebox_Installer::listPackages() function, and its return
     * NOTE: BY DESIGN THIS FAILS IF THERE IS AN IMPROPER CONFIGURE.PHP IN THE MODULES!
     *
	 * @group core.libraries.installer.listPackages
	 * @test
	 */
    public function testListPackages()
    {
        $packages = Bluebox_Installer::listPackages();
        
        foreach ($packages as $package)
        {
            // Sanity check on meta data
            $this->assertEquals(true, isset($package['version']));
            $this->assertEquals(true, is_string($package['packageName']));
            $this->assertEquals(true, is_string($package['displayName']));
            $this->assertEquals(true, is_string($package['author']));
            $this->assertEquals(true, is_string($package['vendor']));
            $this->assertEquals(true, is_string($package['license']));
            $this->assertEquals(true, is_string($package['summary']));
            $this->assertEquals(true, is_string($package['description']));
            $this->assertEquals(true, is_bool($package['default']));
            $this->assertEquals(true, is_bool($package['canBeDisabled']));
            $this->assertEquals(true, is_bool($package['canBeRemoved']));

            // Ensure that categories is as expected
            $this->assertEquals(true, isset($package['type']));

            // Ensure required is set
            $this->assertEquals(true, isset($package['required']));

            // Make sure that we worked out a valid directory
            $this->assertEquals(true, is_dir(MODPATH .$package['directory']));

            // Ensure that the configure class is valid
            $this->assertEquals(true, class_exists($package['configureClass']));

            // Ensure that the models is an array empty or otherwise
            $this->assertEquals(true, is_array($package['models']));

            // Ensure that if this is installed it returns all the proper rows
            if (!is_bool($package['installedAs']))
            {
                $installedAs = $package['installedAs'];
                $this->assertEquals(true, is_numeric($installedAs['module_id']));
                $this->assertEquals(true, is_string($installedAs['name']));
                $this->assertEquals(true, is_string($installedAs['display_name']));
                $this->assertEquals(true, isset($installedAs['module_version']));
                $this->assertEquals(true, is_bool($installedAs['enabled']));
                $this->assertEquals(true, is_bool($installedAs['default']));
                $this->assertEquals(true, is_string($installedAs['basedir']));
            } else {
                $this->assertEquals(false, $package['installedAs']);
            }

            // Ensure this is a valid action
            if (!is_bool($package['action']))
            {
                $this->assertEquals(true, in_array($package['action'], $this->validActions));
            } else {
                $this->assertEquals(false, $package['action']);
            }
        }

        // This ensures that a second loop is equal (because we are using include_once the second time
        // relies entirely on the static var already being valid)
        $secondPackages = Bluebox_Installer::listPackages();
        $this->assertEquals($packages, $secondPackages);

        // Determine from our base line what we would expect if we filtered by modules
        $expected = array();
        foreach ($packages as $name => $package)
        {
            if (in_array(Bluebox_Installer::TYPE_MODULE, $package['type']))
            {
                $expected[$name] = $package;
            }
        }

        // Check the single include filter
        $filteredPackages = Bluebox_Installer::listPackages(Bluebox_Installer::TYPE_MODULE);
        $this->assertEquals($expected, $filteredPackages);

        // Check the lazy version of the same filter
        $filteredPackages = Bluebox_Installer::listPackages(array(Bluebox_Installer::TYPE_MODULE));
        $this->assertEquals($expected, $filteredPackages);

        // Check the explicit version of the same filter
        $filteredPackages = Bluebox_Installer::listPackages(array('include' => Bluebox_Installer::TYPE_MODULE));
        $this->assertEquals($expected, $filteredPackages);

        // Determine from our base line what we would expect if we excluded modules
        $expected = array();
        foreach ($packages as $name => $package)
        {
            if (!in_array(Bluebox_Installer::TYPE_MODULE, $package['type']))
            {
                $expected[$name] = $package;
            }
        }

        // This filter can only be set explicitly
        $filteredPackages = Bluebox_Installer::listPackages(array('exclude' => Bluebox_Installer::TYPE_MODULE));
        $this->assertEquals($expected, $filteredPackages);

        // Determine from our base line what we would expect if we included both modules and drivers
        $expected = array();
        foreach ($packages as $name => $package)
        {
            if (in_array(Bluebox_Installer::TYPE_DRIVER, $package['type']) || in_array(Bluebox_Installer::TYPE_MODULE, $package['type']))
            {
                $expected[$name] = $package;
            }
        }

        // This filter can only be set explicitly
        $filteredPackages = Bluebox_Installer::listPackages(array(
            'include' => array(Bluebox_Installer::CATEGORY_MODULE, Bluebox_Installer::TYPE_DRIVER)
        ));
        $this->assertEquals($expected, $filteredPackages);

        // Determine from our base line what we would expect if we included both modules and drivers
        $expected = array();
        foreach ($packages as $name => $package)
        {
            if (!in_array(Bluebox_Installer::TYPE_DRIVER, $package['type']) && !in_array(Bluebox_Installer::TYPE_MODULE, $package['type']))
            {
                $expected[$name] = $package;
            }
        }

        // This filter can only be set explicitly
        $filteredPackages = Bluebox_Installer::listPackages(array(
            'exclude' => array(Bluebox_Installer::TYPE_MODULE, Bluebox_Installer::TYPE_DRIVER)
        ));
        $this->assertEquals($expected, $filteredPackages);

        // Ensure that if the DB check is disabled everything returns with installedAs == false
        $noDbPackages = Bluebox_Installer::listPackages(array(), true);
        foreach ($noDbPackages as $noDbPackage)
            $this->assertEquals(false, $noDbPackage['installedAs']);
    }

	/**
	 * Tests the Bluebox_Installer::relationTree() function
	 * @group core.libraries.installer.relationTree
	 * @test
	 */
    public function testRelationTree()
    {
        // Get fresh data to work with
        $package1 = $this->numberedPackage(1);
        $package2 = $this->numberedPackage(2);
        $package3 = $this->numberedPackage(3);
        $package4 = $this->numberedPackage(4);

        // Set up known relationships
        $package1['sample1']['required'] = array( 'core' => Bluebox_Controller::$version, 'sample2' => 0.1, 'not' => array('missing' => 0.1) );
        $package2['sample2']['required'] = array( 'core' => Bluebox_Controller::$version );
        $package3['sample3']['required'] = array( 'core' => Bluebox_Controller::$version, 'sample1' => 0.1,  'sample2' => 0.1);
        $packages = array_merge($package1, $package2, $package3, $package4);

        // Ask the installer to work out the relationships
        $relationTree = Bluebox_Installer::relationTree($packages);

        // This is what we should get back
        $expected = array(
            'sample1' => array(
                'dependOn' => array('sample2'),
                'dependOf' => array('sample3'),
            ),
            'sample2' => array(
                'dependOf' => array('sample1', 'sample3'),
                'dependOn' => array(),
            ),
           'sample3' => array(
                'dependOn' => array('sample1', 'sample2'),
                'dependOf' => array(),
            ),
           'sample4' => array(
                'dependOn' => array(),
                'dependOf' => array(),
            )
        );

        // Did we?
        $this->assertEquals($expected, $relationTree);
    }

	/**
	 * Tests the Bluebox_Installer::checkDependencies() function
	 * @group core.libraries.installer.checkDependencies
	 * @test
	 */
    public function testCheckDependencies()
    {
        // Get fresh data to work with
        $package1 = $this->numberedPackage(1);
        $package1['sample1']['configureClass'] = 'Issue_Configure';

        // Ensure that _checks on packages with no action are ignored
        $package1['sample1']['action'] = false;
        Bluebox_Installer::checkDependencies($package1);
        $this->assertEquals(true, empty(Bluebox_Installer::$errors));
        $this->assertEquals(true, empty(Bluebox_Installer::$warnings));

        // Set up for the next test
        $expectedErrors = array (
            'sample1' => array (
                'generic' => 'Unspecified error!',
                'error returned as string',
                'error returned as array',
                'error returned as subarray',
                'error by exception'
            )
        );

        $expectedWarnings = array (
            'sample1' => array (
                'warning returned as array',
                'warning returned as subarray'
            )
        );
        
        // Run through all the actions, and test if _check methods set errors or warnings when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies($package1, 'sample1');

            if (in_array($validAction, $runForActions))
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals($expectedWarnings, Bluebox_Installer::$warnings);        
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));                     
            }
        }

        // Setup for the next test
        $package1['sample1']['configureClass'] = 'Empty_Configure';
        $package1['sample1']['required'] = array( 'core' => Bluebox_Controller::$version + 1);

        $expectedErrors = array (
            'sample1' => array (
                'This module is incompatable with Bluebox version ' .number_format(Bluebox_Controller::$version, 1)
            )
        );

        // Run through all the actions, and ensure core requirements fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies($package1, 'sample1');

            if (in_array($validAction, $runForActions))
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Setup for the next test
        $package1['sample1']['required'] = array( 'nonexistant' => 0.5);

        $expectedErrors = array (
            'sample1' => array (
                'The required module nonexistant could not be found'
            )
        );

        // Run through all the actions, and ensure missing requirements fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies($package1, 'sample1');

            if (in_array($validAction, $runForActions))
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Setup for the next test
        $package1['sample1']['required'] = array('sample2' => 0.5);

        $package2 = $this->numberedPackage(2);
        $package2['sample2']['configureClass'] = 'Empty_Configure';
        $installedAs2 = $package2['sample2']['installedAs'];
        $package2['sample2']['installedAs'] = false;

        $expectedErrors = array (
            'sample1' => array (
                'The required version of sample2 could not be found'
            )
        );

        // Run through all the actions, and ensure uninstalled, incompatiable requirements fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validAction, $runForActions))
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Setup for the next test
        $package2['sample2']['version'] = 0.5;

        $expectedErrors = array (
            'sample1' => array (
                'displayName2 exists but is not installed'
            )
        );

        // Run through all the actions, and ensure missing requirements fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validAction, $runForActions))
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Setup for the next test
        $package2['sample2']['installedAs'] = $installedAs2;
        $package2['sample2']['installedAs']['enabled'] = false;
        
        $expectedErrors = array (
            'sample1' => array (
                'This module is incompatable with displayName2 version 0.1'
            )
        );

        // Run through all the actions, and ensure requirements scheduled for uninstall fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validAction, $runForActions)) {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Setup for the next test
        $package2['sample2']['installedAs']['module_version'] = 0.5;

        $expectedErrors = array (
            'sample1' => array (
                'This module requires displayName2 to also be enabled'
            )
        );

        $expectedWarnings = array (
            'sample1' => array (
                'The required module displayName2 is disabled so this will default to disabled as well'
            )
        );

        // Run through all the actions, and ensure requirements scheduled for uninstall fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if ($validAction == 'enable')
            {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else if (in_array($validAction, $runForActions)) {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals($expectedWarnings, Bluebox_Installer::$warnings);
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        $package2['sample2']['installedAs']['enabled'] = true;
        $package2['sample2']['installedAs']['module_version'] = 0.1;

        $expectedErrors = array (
            'sample1' => array (
                'This module is incompatable with displayName2 version 0.1'
            )
        );

        // Run through all the actions, and ensure requirements scheduled for uninstall fail when expected
        $runForActions = array('downgrade', 'install', 'upgrade', 'enable', 'repair', 'verify');
        foreach ($this->validActions as $validAction)
        {
            $package1['sample1']['action'] = $validAction;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validAction, $runForActions)) {
                $this->assertEquals($expectedErrors, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals(true, empty(Bluebox_Installer::$errors));
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Set up for the next test
        $package2['sample2']['action'] = 'install';

        // If the db version is invalid but the action will upgrade ensure the new version is used
        Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');
        $this->assertEquals(true, empty(Bluebox_Installer::$errors));
        $this->assertEquals(true, empty(Bluebox_Installer::$warnings));

        // On to the complex requirements!
        
        // Get fresh data to work with
        $package1 = $this->numberedPackage(1);
        $package2 = $this->numberedPackage(2);

        // Set up for the next test
        $package2['sample2']['installedAs'] = false;
        $package2['sample2']['version'] = 0.5;

        $expectedMissing = array (
            'sample1' => array (
                'The required version of sample2 could not be found'
            )
        );

        $expectedError = array (
            'sample1' => array (
                'displayName2 exists but is not installed'
            )
        );

        $validOperators = array('<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne');
        $failingOperators = array('<', 'lt', '>', 'gt', '!=', 'ne');

        // Check the operators on if the versions are equal
        foreach ($validOperators as $validOperator)
        {
            $package1['sample1']['required'] = array('sample2' => $validOperator .' 0.5');
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validOperator, $failingOperators))
            {
                $this->assertEquals($expectedMissing, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals($expectedError, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Set up for the next test
        $package2['sample2']['version'] = 0.2;
        $failingOperators = array('>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=');

        // Check the operators on the required package version is less then specified
        foreach ($validOperators as $validOperator)
        {
            $package1['sample1']['required'] = array('sample2' => $validOperator .' 0.5');
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validOperator, $failingOperators))
            {
                $this->assertEquals($expectedMissing, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals($expectedError, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Set up for the next test
        $package2['sample2']['version'] = 0.7;
        $failingOperators = array('<', 'lt', '<=', 'le', '==', '=', 'eq', '!=');

        // Check the operators on the required package version is greater then specified
        foreach ($validOperators as $validOperator)
        {
            $package1['sample1']['required'] = array('sample2' => $validOperator .' 0.5');
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if (in_array($validOperator, $failingOperators))
            {
                $this->assertEquals($expectedMissing, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals($expectedError, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Set up for the next test
        $package1['sample1']['required'] = array('sample2' => '> 0.5', 'sample2' => '< 0.7');

        // Check if a range works
        for ($i = 4; $i < 8; $i +=1)
        {
            $package2['sample2']['version'] = $i;
            Bluebox_Installer::checkDependencies(array_merge($package1, $package2), 'sample1');

            if ($i > 0.5 && $i < 0.7)
            {
                $this->assertEquals($expectedError, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            } else {
                $this->assertEquals($expectedMissing, Bluebox_Installer::$errors);
                $this->assertEquals(true, empty(Bluebox_Installer::$warnings));
            }
        }

        // Get fresh data to work with
        $package1 = $this->numberedPackage(1);
        $package2 = $this->numberedPackage(2);

        // Set up for the next test
        $package1['sample1']['action'] = 'disable';
        $package2['sample2']['required'] = array('sample1' => '0.1');
        
        $expectedError = array (
            'sample1' => array (
                'This module is used by displayName2, so it must also be disabled'
            )
        );

        // Make sure you can not disable or unistall a module that is required by an active module
        Bluebox_Installer::checkDependencies(array_merge($package1, $package2));
        $this->assertEquals($expectedError, Bluebox_Installer::$errors);

        $package1['sample1']['action'] = 'uninstall';
        Bluebox_Installer::checkDependencies(array_merge($package1, $package2));
        $this->assertEquals($expectedError, Bluebox_Installer::$errors);


        $package1['sample1']['action'] = false;
        $package2['sample2']['action'] = 'install';
        $package2['sample2']['required'] = array(
            'not' => array('sample1' => '0.1')
        );

        $expectedError = array (
            'sample1' => array (
                'This module can not be installed with displayName2'
            ),
            'sample2' => array (
                'This module can not be installed with displayName1'
            )
        );

        // Test a 'not' module
        Bluebox_Installer::checkDependencies(array_merge($package1, $package2));
        $this->assertEquals($expectedError, Bluebox_Installer::$errors);
    }
    
	/**
	 * Tests the Bluebox_Installer::determineActions() function
	 * @group core.libraries.installer.determineActions
	 * @test
	 */
    public function testDetermineActions()
    {
        // Get fresh data to work with
        $package = $this->singlePackage();

        // With no install list the package should be unchanged
        $testResult = Bluebox_Installer::determineActions($package);
        $this->assertEquals($package, $testResult);

        // With the wrong package install list the package should be unchanged
        $testResult = Bluebox_Installer::determineActions($package, 'nonexistant');
        $this->assertEquals($package, $testResult);

        // When versions are identical we expect install
        $testResult = Bluebox_Installer::determineActions($package, 'sample');
        $this->assertEquals('install', $testResult['sample']['action']);

        // When the installed version is larger we expect downgrade
        for ($i = 0; $i < 50; $i += 1)
        {
            $rand1 = $this->random_float(0.1, 3);
            $rand2 = $this->random_float(0.1, 3);

            if ($rand1 > $rand2)
            {
                $package['sample']['installedAs']['module_version'] = $rand1;
                $package['sample']['version'] = $rand2;
                $testResult = Bluebox_Installer::determineActions($package, 'sample');
                $this->assertEquals('downgrade', $testResult['sample']['action']);
            } else if ($rand1 < $rand2) {
                $package['sample']['installedAs']['module_version'] = $rand2;
                $package['sample']['version'] = $rand1;
                $testResult = Bluebox_Installer::determineActions($package, 'sample');
                $this->assertEquals('downgrade', $testResult['sample']['action']);
            } else {
                $i -= 1;
            }
        }
        
        // When the installed version is smaller we expect upgrade
        for ($i = 0; $i < 50; $i += 1)
        {
            $rand1 = $this->random_float(0.1, 3);
            $rand2 = $this->random_float(0.1, 3);

            if ($rand1 < $rand2)
            {
                $package['sample']['installedAs']['module_version'] = $rand1;
                $package['sample']['version'] = $rand2;
                $testResult = Bluebox_Installer::determineActions($package, 'sample');
                $this->assertEquals('upgrade', $testResult['sample']['action']);
            } else if ($rand1 > $rand2) {
                $package['sample']['installedAs']['module_version'] = $rand2;
                $package['sample']['version'] = $rand1;
                $testResult = Bluebox_Installer::determineActions($package, 'sample');
                $this->assertEquals('upgrade', $testResult['sample']['action']);
            } else {
                $i -= 1;
            }
        }

        // When there is no installedAs data we expect install
        $package['sample']['installedAs'] = false;
        $testResult = Bluebox_Installer::determineActions($package, 'sample');
        $this->assertEquals('install', $testResult['sample']['action']);

        // Get fresh data to work with
        $package = $this->singlePackage();

        // If we process with a invalid action then make sure it gets cleared
        $package['sample']['action'] = 'this is not a valid action';
        $testResult = Bluebox_Installer::determineActions($package);
        $this->assertEquals(false, $testResult['sample']['action']);

        // If we process with a invalid action but we are scheduled for install, then it should be overridden
        $testResult = Bluebox_Installer::determineActions($package, 'sample');
        $this->assertEquals('install', $testResult['sample']['action']);

        // If we process with a valid action it should not be changed
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::determineActions($package);
            $this->assertEquals($package, $testResult);
        }

        // If we process with a valid action it should not be changed, even if we are scheduled for install
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::determineActions($package, 'sample');
            $this->assertEquals($package, $testResult);
        }
    }

	/**
	 * Tests the Bluebox_Installer::packageAvailable() function
	 * @group core.libraries.installer.packageAvailable
	 * @test
	 */
    public function testPackageAvailable()
    {
        // Get fresh data to work with
        $package = $this->singlePackage();
        $installedAs = $package['sample']['installedAs'];

        // If the module is not in the list then it is considered -1 (missing)
        $package['sample']['installedAs'] = false;
        $testResult = Bluebox_Installer::packageAvailable(array(), 'sample');
        $this->assertEquals(-1, $testResult);

        // Run all actions on a uninstalled package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            if ($validAction == 'downgrade' || $validAction == 'install' || $validAction == 'upgrade')
            {
                // If the module is scheduled for installation and is intended to be enabled then we expect 2 otherwise 1
                if ($package['sample']['default'])
                    $this->assertEquals(2, $testResult);
                else
                    $this->assertEquals(1, $testResult);
            } else {
                // If the package is uninstalled and not scheduled to be installed we expect 0
                $this->assertEquals(0, $testResult);
            }
        }

        // Set up for the next test
        $package['sample']['installedAs'] = $installedAs;
        $package['sample']['installedAs']['enabled'] = false;

        // Run all actions on an installed but disabled package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            if ($validAction == 'uninstall')
            {
                // If the package is scheduled for removal then we expect 0
                $this->assertEquals(0, $testResult);
            } else if ($validAction == 'enable') {

                // If the module is scheduled to be enabled then we expect 2
                $this->assertEquals(2, $testResult);
            } else if ($validAction == 'downgrade' || $validAction == 'install' || $validAction == 'upgrade' ) {

                // If the package is going to be downgraded, re-installed, or upgraded then we expect 2 if it will be
                // enabled post action or 1 if not
                if ($package['sample']['default'])
                    $this->assertEquals(2, $testResult);
                else
                    $this->assertEquals(1, $testResult);
            } else {

                // If the action can not change its status we expect 1
                $this->assertEquals(1, $testResult);
            }
        }

        // Set up for the next test
        $package['sample']['installedAs'] = $installedAs;
        $package['sample']['installedAs']['enabled'] = true;

        // Run all actions on an installed package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            if ($validAction == 'uninstall')
            {
                // If the package is scheduled for removal then we expect 0
                $this->assertEquals(0, $testResult);
            } else if ($validAction == 'disable') {

                // If the module is scheduled to be disabled then we expect 1
                $this->assertEquals(1, $testResult);
            } else if ($validAction == 'downgrade' || $validAction == 'install' || $validAction == 'upgrade' ) {

                // If the package is going to be downgraded, re-installed, or upgraded then we expect 2 if it will be
                // enabled post action or 1 if not
                if ($package['sample']['default'])
                    $this->assertEquals(2, $testResult);
                else
                    $this->assertEquals(1, $testResult);
            } else {

                // If the action can not change its status we expect 2
                $this->assertEquals(2, $testResult);
            }
        }

        // We are going to re-run all the tests but with errors which should void all actions

        Bluebox_Installer::$errors['sample'] = 'THIS WILL CHANGE EXPECTED RESULTS';

        // Get fresh data to work with
        $package = $this->singlePackage();
        $installedAs = $package['sample']['installedAs'];

        // If the module is not in the list then it is considered -1 (missing)
        $package['sample']['installedAs'] = false;
        $testResult = Bluebox_Installer::packageAvailable(array(), 'sample');
        $this->assertEquals(-1, $testResult);

        // Run all actions on a uninstalled package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            // If the package is uninstalled and has errors we expect 0
            $this->assertEquals(0, $testResult);
        }

        // Set up for the next test
        $package['sample']['installedAs'] = $installedAs;
        $package['sample']['installedAs']['enabled'] = false;

        // Run all actions on an installed but disabled package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            // If the package is installed but disabled and has errors we expect 1
            $this->assertEquals(1, $testResult);
        }

        // Set up for the next test
        $package['sample']['installedAs'] = $installedAs;
        $package['sample']['installedAs']['enabled'] = true;

        // Run all actions on an installed package that is in the list
        foreach ($this->validActions as $validAction)
        {
            $package['sample']['action'] = $validAction;
            $testResult = Bluebox_Installer::packageAvailable($package, 'sample');

            // If the package is installed and has errors we expect 2
            $this->assertEquals(2, $testResult);
        }
    }

	/**
	 * Tests the Bluebox_Installer::dependencySort() function
	 * @group core.libraries.installer.dependencySort
	 * @test
	 */
    public function testDependencySort()
    {
        // Get fresh data to work with
        $package1 = $this->numberedPackage(1);
        $package2 = $this->numberedPackage(2);
        $package3 = $this->numberedPackage(3);

        // If they have no action then they are avaliable at any time (or checkDependencies would have failed)
        // and the sorter will ignore them...
        $package1['sample1']['action'] = 'install';
        $package2['sample2']['action'] = 'install';
        $package3['sample3']['action'] = 'install';

        // Set up known relationships
        $package1['sample1']['required'] = array( 'core' => Bluebox_Controller::$version, 'sample2' => 0.1 );
        $package2['sample2']['required'] = array( 'core' => Bluebox_Controller::$version );
        $package3['sample3']['required'] = array( 'core' => Bluebox_Controller::$version, 'sample1' => 0.1,  'sample2' => 0.1);

        // The expected order
        $expected = array (
            'sample2',
            'sample1',
            'sample3'
        );

        $packages = array_merge($package1, $package2, $package3);
        Bluebox_Installer::_dependencySort($packages);
        $this->assertEquals($expected, array_keys($packages));

        $packages = array_merge($package3, $package2, $package1);
        Bluebox_Installer::_dependencySort($packages);
        $this->assertEquals($expected, array_keys($packages));

        $packages = array_merge($package3, $package1, $package2);
        Bluebox_Installer::_dependencySort($packages);
        $this->assertEquals($expected, array_keys($packages));

        $packages = array_merge($package2, $package1, $package3);
        Bluebox_Installer::_dependencySort($packages);
        $this->assertEquals($expected, array_keys($packages));
    }

    /**
     * This function is used to provide random floats for versions
     */
    public function random_float ($min,$max) {
       return round(($min+lcg_value()*(abs($max-$min))),1);
    }

	/**
	 * DataProvider for tests
	 */
    public function singlePackage()
    {
        return array (
            'sample' => array (
                'version' => 0.1,
                'packageName' => 'packageName',
                'author' => 'author',
                'vendor' => 'vendor',
                'license' => 'license',
                'summary' => 'summary',
                'description' => 'description',
                'default' => true,
                'categories' => Bluebox_Installer::TYPE_PLUGIN,
                'required' => array ( 'core' => Bluebox_Controller::$version ),
                'displayName' => 'displayName',
                'canBeDisabled' => true,
                'canBeRemoved' => true,
                'directory' => 'directory',
                'configureClass' => 'Empty_Configure',
                'installedAs' => array (
                    'module_id' => '1',
                    'name' => 'dbPackageName',
                    'display_name' => 'dbDisplayName',
                    'module_version' => '0.1',
                    'enabled' => true,
                    'default' => true,
                    'config_class' => 'dbConfigureClass',
                    'basedir' => 'dbDirectory',
                    'created_at' => '2009-08-17 15:18:41',
                    'updated_at' => '2009-08-17 15:18:41',
                    'version' => '1'
                ),
                'action' => false,
                'models' => array (
                    'model1' => 'model1',
                    'model2' => 'model2',
                    'model3' => 'model3',
                )
            )
        );
    }


	/**
	 * DataProvider for tests
	 */
    public function numberedPackage($num)
    {
        return array (
            'sample'.$num => array (
                'version' => 0.1,
                'packageName' => 'packageName'.$num,
                'author' => 'author'.$num,
                'vendor' => 'vendor'.$num,
                'license' => 'license'.$num,
                'summary' => 'summary'.$num,
                'description' => 'description'.$num,
                'default' => true,
                'categories' => Bluebox_Installer::TYPE_PLUGIN,
                'required' => array ( 'core' => Bluebox_Controller::$version ),
                'displayName' => 'displayName'.$num,
                'canBeDisabled' => true,
                'canBeRemoved' => true,
                'directory' => 'directory'.$num,
                'configureClass' => 'Empty_Configure',
                'installedAs' => array (
                    'module_id' => '1',
                    'name' => 'dbPackageName'.$num,
                    'display_name' => 'dbDisplayName'.$num,
                    'module_version' => '0.1',
                    'enabled' => true,
                    'default' => true,
                    'config_class' => 'dbConfigureClass'.$num,
                    'basedir' => 'dbDirectory'.$num,
                    'created_at' => '2009-08-17 15:18:41',
                    'updated_at' => '2009-08-17 15:18:41',
                    'version' => '1'
                ),
                'action' => false,
                'models' => array (
                    'model1' => 'model1',
                    'model2' => 'model2',
                    'model3' => 'model3',
                )
            )
        );
    }
}

class Issue_Configure extends Bluebox_Configure
{
    public static function _checkIgnored()
    {
        return true;
    }

    public static function _checkNoMsg()
    {
        return false;
    }

    public static function _checkString()
    {
        return 'error returned as string';
    }

    public static function _checkErrorArray()
    {
        return array('errors' => 'error returned as array');
    }

    public static function _checkErrorSubArray()
    {
        return array('errors' => array('error returned as subarray'));
    }

    public static function _checkErrorException()
    {
        throw new Exception('error by exception');
    }

    public static function _checkWarningArray()
    {
        return array('warnings' => 'warning returned as array');
    }

    public static function _checkWarningSubArray()
    {
        return array('warnings' => array('warning returned as subarray'));
    }
}

class Empty_Configure extends Bluebox_Configure
{


}

function __($msg)
{
    return $msg;
}