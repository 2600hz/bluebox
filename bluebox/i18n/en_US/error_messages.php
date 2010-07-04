<?php defined('SYSPATH') or die('No direct access allowed.');

$lang = array
(
'default' => Array
    (
        'required' => 'Field is required',
        'default' => 'Field contains an error'
    ),
'first_name' => Array
    (
        'required' => 'First name is required.',
        'alpha' => 'Only letters are allowed.',
        'length' => 'Must be 3-40 characters.',
        'default' => 'Invalid First Name.',
    ),
'last_name' => Array
    (
        'required' => 'Last name is required.',
        'alpha' => 'Only letters are allowed.',
        'length' => 'Must be 3-40 characters.',
        'default' => 'Invalid Last Name.',
    ),
'company_name' => Array
    (
        'required' => 'Company name is required.',
        'alpha' => 'Only letters are allowed.',
        'length' => 'Must be 3-50 characters.',
        'default' => 'Invalid Company Name.',
    ),
'street' => Array
    (
        'required' => 'Street address is required.',
        'alpha' => 'Only letters are allowed.',
        'length' => 'Must be 3+ characters.',
        'default' => 'Invalid Street Address.',
    ),
'zip' => Array
    (
        'required' => 'Zip code is required.',
        'numeric' => 'Only numbers are allowed.',
        'default' => 'Invalid Zip Code.',
    ),
'email_address' => Array
    (
        'required' => 'Email is required.',
        'duplicate' => 'Email is already in use.',
        'email' => 'Email is invalid.',
        'unknown' => 'Email address is not recognized.',
        'default' => 'Invalid Email Address.',
    ),
'password' => Array
    (
        'required' => 'Password is required.',
        'nodigits' => 'Letters & digits required',
        'noalpha' => 'Letters & digits required',
        'length' => 'Must be 5-20 characters',
//        'pwd_check' => 'The password is not correct.',
        'default' => 'Invalid password.',
    ),
'password2' => Array
    (
        'nomatch' => 'Passwords do not match.'
    ),
    'confirm_password' => array(
        'nomatch' => 'Passwords do not match',
    ),
'cc_number' => Array
    (
        'required' => 'Credit card number is required.',
        'numeric' => 'Only numbers are allowed.',
        'length' => 'Must be 12+ numbers.',
        'default' => 'Invalid credit card number.',
    ),
'cc_expmon' => Array
    (
        'required' => 'Expiration month is required.',
        'numeric' => 'Expiration must be numbers.',
        'length' => 'Must be 2 digit month.',
        'default' => 'Invalid expiration month.',
    ),
'cc_expyear' => Array
    (
        'required' => 'Expiration year is required.',
        'numeric' => 'Expiration must be numbers.',
        'length' => 'Must be 2 digit year.',
        'default' => 'Invalid expiration year.',
    ),
'cc_ccv' => Array
    (
        'required' => 'Credit card verification code is required.',
        'numeric' => 'CCV must be all numbers.',
        'length' => 'Must be 3 or 4 digits.',
        'default' => 'Invalid CCV code.',
    ),
'token' => Array
    (
        'required' => 'A token must be entered.',
        'default' => 'Invalid token.',
    ),
);