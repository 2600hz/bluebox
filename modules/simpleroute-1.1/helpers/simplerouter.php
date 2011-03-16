<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Simpleroute
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class simplerouter
{
   public static function getOutboundPattern($simple_route_id, $engine = 'freeswitch')
    {
       $simpleRoute = Doctrine::getTable('SimpleRoute')->find($simple_route_id);

       if (empty($simpleRoute['patterns']))
       {
           return FALSE;
       }

        // get the outbound patterns out of the record
        $patterns = $simpleRoute['patterns'];

        switch($simpleRoute['type'])
        {
            case SimpleRoute::TYPE_REGEX:
                $convert = FALSE;

                break;
            
            case SimpleRoute::TYPE_SIMPLE:
            default:
                $convert = TRUE;
        }

        $patterns = $simpleRoute['patterns'];

        // based on the requesting engine generate the pattern required
        switch(strtolower($engine))
        {
            case 'freeswitch':
                // Loop each rule and build a regex
                $pattern = Array();

                foreach ($patterns as $rule)
                {
                    if (!$convert)
                    {
                        array_push($pattern, $rule);

                        continue;
                    }

                    if (preg_match('/^[0-9]*$/', $rule))
                    {
			if (preg_match('/\(/',$rule)) 
			{
	                        array_push($pattern, '^' . $rule . '$');
			} 
			else 
			{
				array_push($pattern, '^(' . $rule . ')$');
			}

                        continue;
                    }

                    $rule = self::npaToRegex($rule);

                    if (empty($rule))
                    {
                        continue;
                    }
                    
                    // Convert the short hand into regex
		    if (preg_match('/\(/',$rule)) 
		    {
			array_push($pattern, '^' . $rule . '$');
		    } 
		    else 
		    {
			array_push($pattern, '^(' . $rule . ')$');
		    }
                }

                // we added a pipe on the end of every rule so remove the last one
                //$pattern = str_replace(array('{', '}'), array('\{', '\}'), $pattern);
                return $pattern;
                
            case 'asterisk':
                // Loop each rule and build a regex
                $pattern = array();

                foreach ($patterns as $rule)
                {
                    if (!$convert)
                    {
                        $pattern .= $rule .'|';

                        continue;
                    }
                    
                    if (preg_match('/^[0-9]*$/', $rule))
                    {
                        $pattern .= '^' . $rule . '$|';

                        continue;
                    }
                    
                    $rule = self::npaToAsteriskShort($rule);

                    if (empty($rule))
                    {
                        continue;
                    }
                    
                    // Convert the short hand into regex
                    $pattern += $rule;
                }

                // because of the asterisk restrictions we return an array of
                // patterns that make up the regex groups in freeswitch
                return $pattern;
                
            default:
                return FALSE;
        }
    }

    public static function npaToAsteriskShort($pattern = NULL)
    {
        if (empty($pattern))
        {
            return FALSE;
        }

        $pattern = str_replace(array('A', 'P', ' '), array('X', '[0-8]', ''), $pattern);

        // This is nasty but asterisk does not support a conditional match....
        // ...TO THE PERMUTATION ENGINE!
        $permutations = self::conditionPermutations($pattern);

        // Break the permutations into an array of patterns
        $patterns = explode('|', $permutations);

        // for each pattern we need two parts in asterisk, one that matches the
        // dialed number, and one to get the requested parts out of the number
        $results = array();

        foreach($patterns as $pattern)
        {
            // for the pattern used by asterisk remove the parentheses
            $cleanPattern = str_replace(array('(', ')'), array('', ''), $pattern);

            // if the pattern is just numbers then let asterisk handle it as
            // an extension
            if (!preg_match('/^[0-9]*$/', $cleanPattern))
            {
                $cleanPattern = '_' .$cleanPattern;
            }

            // for the actually outbound dial we need a rule about which
            // numbers to use from the pattern
            $exten = '${EXTEN';

            // we dont care about parentheses on the far left or right (IE: full
            // number or rest of number)
            $pattern = trim($pattern, '()');

            // find the first occurance of ( and if there that is our offset
            if ($pos = strpos($pattern, '('))
            {
                $exten .= ':' .$pos;
            }

            // find the last occurance of ) and if there that is our length
            if ($pos = strrpos($pattern, ')'))
            {
                $exten .= ':' .$pos;
            }

            // close this up
            $exten .= '}';

            // this array is key => value where key is the pattern necessary to
            // enter the dialplan and the value is a ${EXEN} construct with the
            // necessary offset and length
            $results[$cleanPattern] = $exten;
        }
        
        return $results;
    }

    // This is a recursive function that will return a list of all possible
    // permutaions around the conditional match ? token
    public static function conditionPermutations($pattern)
    {
        if ($pos = strpos($pattern, '?'))
        {
            $patternWith = substr($pattern, 0, $pos) .substr($pattern, $pos+1);

            $patternWithOut = substr($pattern, 0, $pos-1) .substr($pattern, $pos+1);

            return self::conditionPermutations($patternWith) .'|' .self::conditionPermutations($patternWithOut);
        } 
        else
        {
            return $pattern;
        }
    }

    public static function npaToRegex($pattern = NULL)
    {
        if (empty($pattern))
        {
            return FALSE;
        }

        // split the npa into an pattern
        $pattern = str_split($pattern);

        $iteration = 1;

        $prevRule = '';

        foreach ($pattern as $key => $rule)
        {
            // remove spaces
            if ($rule == ' ')
            {
                unset($pattern[$key]);
                
                continue;
            }

            // if this is not escaped then replace the pattern
            if ($prevRule != '\\')
            {
                // find our pattern stand ends and replace them
                switch($rule)
                {
                    case 'X':
                    case 'A':
                        $pattern[$key] = '[0-9]';

                        break;

                    case 'P':
                        $pattern[$key] = '[0-8]';

                        break;

                    case 'Z':
                        $pattern[$key] = '[1-9]';

                        break;

                    case 'N':
                        $pattern[$key] = '[2-9]';

                        break;

                    case '!':
                        $pattern[$key] = '.*';

                        break;

                    case '.':
                        $pattern[$key] = '.+';

                        break;

                    case '?':
                        $pattern[$key] = '{0,1}';

                        break;
                }

                // update the rule so we process this correctly
                $rule = $pattern[$key];
            } 
            else
            {
                // if the previous rule was an escape remove the marker
                unset($pattern[$key - 1]);
            }

            if ($rule == $prevRule)
            {
                // if the rule is the same as the last inc iteration of the rule
                unset($pattern[$key]);

                $iteration++;
            } 
            else if ($iteration > 1)
            {
                // if we where tracking iterations and are at a new rule
                // then add in the iteration count are rest iteration
                // 
                // if the next element is itself an iterator handle it diff
                if (in_array($rule, array('.*', '.+')))
                {
                    // since the next rule is an iterator we need to put
                    // back something for it to iterate against
                    $iteration--;

                    // if that action reduced us to one then we dont need
                    // an iterator after all
                    if ($iteration > 1)
                    {
                        $pattern[$key] = '{' . $iteration . '}'. $prevRule . $rule;
                    } 
                    else
                    {
                        $pattern[$key] = $prevRule . $rule;
                    }
                } 
                else
                {
                    // if this is a iterated element followed by a non iterator
                    // print our iterator and the new rule, no need to
                    // backtrack for a pattern
                    $pattern[$key] = '{' . $iteration . '}'. $rule;
                }

                $prevRule = $rule;

                $iteration = 1;
            }
            else
            {
                // otherwise just track the rules
                $prevRule = $rule;
            }
        }

        // if we left the loop with a final iteration then add that
        if ($iteration > 1)
        {
            $pattern[] = '{' . $iteration . '}';
        }

        // make our pattern from the array parts
        $pattern = implode('', $pattern);

        // if there is no opening parentheses assume it
        // belongs on the front
        if (!strstr($pattern, '('))
        {
            $pattern = '(' . $pattern;
        }

        // if there is no closing parentheses assume it
        // belongs on the end
        if (!strstr($pattern, ')'))
        {
            $pattern .= ')';
        }

        // return out regex
        return $pattern;
    }
}
