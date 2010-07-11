<?php
/**
 * Patterns
 * ----------------------------------------------------------------
 *      X,A         matches any digit from 0-9
 *       P          matches any digit from 0-8
 *       Z          matches any digit from 1-9
 *       N          matches any digit from 2-9
 *    [1237-9]      matches any digit or letter in the brackets
 *                      (in this example, 1,2,3,7,8,9)
 *       .          wildcard, matches one or more characters
 *       !          wildcard, matches zero or more characters immediately
 *      ( )         Creates the number group used to dial out
 *       ?          makes the preceding number optional
 *
 *
 * Important Notes
 * ---------------------------------------------------------------
 *  ->   Use the parentheses to group the numbers you want to use for
 *       outbound dialing.  If you do not have a group the entire
 *       pattern will be used.
 *  ->   For convience you may place spaces in the patterns, these will
 *       be removed for you
 *  ->   If you are are attempting to include any of the above pattern
 *       charaters (such as X), you must escape it via \ example \X.
 *  ->   All patterns must be provided as uppercase (where appropriate)
 *
 * Example 1
 * ---------------------------------------------------------------
 *       Pattern = 1?(XXXXXXXXX)
 *       Number Dialed = 15558675309
 *       As Sent to Trunk = {trunk_prepend}5558675309
 *       ---------------------------------------------------------
 *       In this case the first 1 is optional (as indicated by the
 *       explanation point after it) and if present is outside the
 *       number groups used to send to the trunk.
 *
 *
 * Example 2
 * ---------------------------------------------------------------
 *       Pattern = 91?(XXXXXXXXX)
 *       Number Dialed = 95558675309
 *       As Sent to Trunk = {trunk_prepend}5558675309
 *       ---------------------------------------------------------
 *       In this case you must dial 9 first, then an optional 1 followed
 *       by exactly 10 digits.  Both the 9 and, if present, the 1 will
 *       NOT be sent to the trunk.
 */

// These are the local rules, allowing local dialing as well as to
// information and city services
$config['route_display']['local'] = '10-digit US dialing';
$config['outbound_patterns']['local'][] = '1?(NPA NXX XXXX)';
$config['outbound_patterns']['local'][] = '411';
$config['outbound_patterns']['local'][] = '611';

// this allows dialing without the default area code
$config['outbound_patterns']['short'] = '1?(NXX XXXX)';

// this is the rule for dialing international
$config['route_display']['international'] = 'International dialing (011+)';
$config['outbound_patterns']['international'] = '011(X.)';

// this is the rule for dialing emergency services
$config['route_display']['emergency'] = 'Emergency diailing (911)';
$config['outbound_patterns']['emergency'] = '911';





