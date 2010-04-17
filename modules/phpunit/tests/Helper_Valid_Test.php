<?php
/**
 * Valid Helper Unit Tests
 *
 * @package  Core
 * @author   Jeremy Bush
 * @group    core
 * @group    core.helpers
 * @group    core.helpers.valid
 */
class Helper_Valid_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * DataProvider for the valid::email() test
	 */
	public function email_provider()
	{
		return array(array('address@domain.tld', TRUE),
		             array('address@domain',     FALSE));
	}
	
	/**
	 * Tests the valid::email() function.
	 * @dataProvider email_provider
	 * @group core.helpers.valid.email
	 * @test
	 */
	public function email($input_email, $expected_result)
	{
		$result = valid::email($input_email);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::email_domain() test
	 */
	public function email_domain_provider()
	{
		return array(array('gmail.com', TRUE),
		             array('gmail',     FALSE));
	}

	/**
	 * Tests the valid::email_domain() function.
	 * @dataProvider email_domain_provider
	 * @group core.helpers.valid.email_domain
	 * @test
	 */
	public function email_domain($input_email, $expected_result)
	{
		if ( ! function_exists('checkdnsrr'))
			$this->markTestSkipped('checkdnsrr() is missing from your install.');
			
		$result = valid::email_domain($input_email);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::email_rfc() test
	 */
	public function email_rfc_provider()
	{
		return array(array('foo+bar@foobar.com', TRUE),
		             array('test@192.168.1.1',   TRUE),
		             array('foobar@foobar',      TRUE),
		             array('1@2.3',              TRUE),
		             array('wtf',                FALSE));
	}

	/**
	 * Tests the valid::email_rfc() function.
	 * @dataProvider email_rfc_provider
	 * @group core.helpers.valid.email_rfc
	 * @test
	 */
	public function email_rfc($input_email, $expected_result)
	{
		$result = valid::email_rfc($input_email);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::url() test
	 */
	public function url_provider()
	{
		return array(array('http://www.kohanaphp.com', TRUE),
		             array('www.kohanaphp.com',        FALSE));
	}

	/**
	 * Tests the valid::url() function.
	 * @dataProvider url_provider
	 * @group core.helpers.valid.url
	 * @test
	 */
	public function url($input_url, $expected_result)
	{
		$result = valid::url($input_url);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::ip() test
	 */
	public function ip_provider()
	{
		return array(array('75.125.175.50',   FALSE, TRUE),
		             array('127.0.0.1',       FALSE, TRUE),
		             array('256.257.258.259', FALSE, FALSE),
		             array('255.255.255.255', FALSE, FALSE),
		             array('192.168.0.1',     FALSE, FALSE),
		             array('192.168.0.1',     TRUE,  TRUE));
	}

	/**
	 * Tests the valid::ip() function.
	 * @dataProvider ip_provider
	 * @group core.helpers.valid.ip
	 * @test
	 */
	public function ip($input_ip, $allow_private, $expected_result)
	{
		$result = valid::ip($input_ip, FALSE, $allow_private);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::credit_card() test
	 */
	public function credit_card_provider()
	{
		return array(array('4222222222222',    'visa',       TRUE),
		             array('4012888888881881', 'visa',       TRUE),
		             array('4012888888881881', NULL,         TRUE),
		             array('4012888888881881', array('mastercard', 'visa'), TRUE),
		             array('4012888888881881', array('discover', 'mastercard'), FALSE),
		             array('4012888888881881', 'mastercard', FALSE),
		             array('5105105105105100', 'mastercard', TRUE),
		             array('6011111111111117', 'discover',   TRUE),
		             array('6011111111111117', 'visa',       FALSE));
	}

	/**
	 * Tests the valid::credit_card() function.
	 * @dataProvider credit_card_provider
	 * @group core.helpers.valid.credit_card
	 * @test
	 */
	public function credit_card($input_number, $card_type, $expected_result)
	{
		$result = valid::credit_card($input_number, $card_type);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::phone() test
	 */
	public function phone_provider()
	{
		return array(array('0163634840',   TRUE),
		             array('+27173634840', TRUE),
		             array('123578',       FALSE));
	}

	/**
	 * Tests the valid::phone() function.
	 * @dataProvider phone_provider
	 * @group core.helpers.valid.phone
	 * @test
	 */
	public function phone($input_phone, $expected_result)
	{
		$result = valid::phone($input_phone);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::date() test
	 */
	public function date_provider()
	{
		return array(array('1/1/01',              TRUE),
		             array('January 1, 2001',     TRUE),
		             array('this is not a date.', FALSE));
	}

	/**
	 * Tests the valid::date() function.
	 * @dataProvider date_provider
	 * @group core.helpers.valid.date
	 * @test
	 */
	public function date($input_date, $expected_result)
	{
		$result = valid::date($input_date);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::alpha() test
	 */
	public function alpha_provider()
	{
		return array(array('abcdef',   TRUE),
		             array('12345',    FALSE),
		             array('abcd1234', FALSE));
	}

	/**
	 * Tests the valid::alpha() function.
	 * @dataProvider alpha_provider
	 * @group core.helpers.valid.alpha
	 * @test
	 */
	public function alpha($input_alpha, $expected_result)
	{
		$result = valid::alpha($input_alpha);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::alpha_numeric() test
	 */
	public function alpha_numeric_provider()
	{
		return array(array('abcd1234',  TRUE),
		             array('abcd',      TRUE),
		             array('1234',      TRUE),
		             array('abc123&^/-', FALSE));
	}

	/**
	 * Tests the valid::alpha_numeric() function.
	 * @dataProvider alpha_numeric_provider
	 * @group core.helpers.valid.alpha_numeric
	 * @test
	 */
	public function alpha_numeric($input_alpha_numeric, $expected_result)
	{
		$result = valid::alpha_numeric($input_alpha_numeric);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::alpha_dash() test
	 */
	public function alpha_dash_provider()
	{
		return array(array('abcdef',   TRUE),
		             array('12345',    TRUE),
		             array('abcd1234', TRUE),
		             array('abcd1234-', TRUE),
		             array('abc123&^/-', FALSE));
	}

	/**
	 * Tests the valid::alpha_dash() function.
	 * @dataProvider alpha_dash_provider
	 * @group core.helpers.valid.alpha
	 * @test
	 */
	public function alpha_dash($input_alpha_dash, $expected_result)
	{
		$result = valid::alpha_dash($input_alpha_dash);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::digit() test
	 */
	public function digit_provider()
	{
		return array(array('12345',    TRUE),
		             array('10.5',     FALSE),
		             array('abcde',    FALSE),
		             array('abcd1234', FALSE));
	}

	/**
	 * Tests the valid::digit() function.
	 * @dataProvider digit_provider
	 * @group core.helpers.valid.digit
	 * @test
	 */
	public function digit($input_digit, $expected_result)
	{
		$result = valid::digit($input_digit);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::numeric() test
	 */
	public function numeric_provider()
	{
		return array(array('12345', TRUE),
		             array('10.5',  TRUE),
		             array('-10.5', TRUE),
		             array('10.5a', FALSE));
	}

	/**
	 * Tests the valid::numeric() function.
	 * @dataProvider numeric_provider
	 * @group core.helpers.valid.numeric
	 * @test
	 */
	public function numeric($input_numeric, $expected_result)
	{
		$result = valid::numeric($input_numeric);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the valid::decimal() test
	 */
	public function decimal_provider()
	{
		return array(array('10.2', NULL,       TRUE),
                     array('145.23', array(2),   TRUE),
		             array('10.2', array(2,1), TRUE),
		             array('10.2', array(3,1), FALSE),
		             array('10.2', array(2,2), FALSE));
	}

	/**
	 * Tests the valid::decimal() function.
	 * @dataProvider decimal_provider
	 * @group core.helpers.valid.decimal
	 * @test
	 */
	public function decimal($input_decimal, $decimal_format, $expected_result)
	{
		$result = valid::decimal($input_decimal, $decimal_format);
		$this->assertEquals($expected_result, $result);
	}
}
