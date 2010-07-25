<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Form
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class form extends form_Core
{
    /**
     * This var allows users to add classes to any or all form elements.  Array
     * keys can the name of a form method or the word 'all'.  The value can
     * be a single class as a string or an array of classes.
     * Example: adding test1 and test2 classes to inputs
     *
     * form::$customClasses = array('inputs' => array('test1', 'test2'));
     *
     * @var array
     */
    public static $customClasses = array();

    /**
     * This is a cache of repopulate values so we dont have to look them up
     * for each field
     * @var array
     */
    private static $repopulateValues = array();

    /**
     * Generates an opening HTML form tag.
     *
     * @param   string  form action attribute
     * @param   array   extra attributes
     * @param   array   hidden fields to be created immediately after the form tag
     * @return  string
     */
    public static function open($action = NULL, $attr = array(), $hidden = NULL)
    {
        // Inject any un-specified defaults for this function
        list($action, $attr, $hidden) = self::_addDefaults(__FUNCTION__, $action, $attr, $hidden);

        // Call the parent
        $result = parent::open($action, $attr, $hidden);
        
        return $result;
    }

    /**
     * Generates an opening HTML form tag that can be used for uploading files.
     *
     * @param   string  form action attribute
     * @param   array   extra attributes
     * @param   array   hidden fields to be created immediately after the form tag
     * @return  string
     */
    public static function open_multipart($action = NULL, $attr = array(), $hidden = array())
    {
        // Inject any un-specified defaults for this function
        list($action, $attr, $hidden) = self::_addDefaults(__FUNCTION__, $action, $attr, $hidden);

        // Call the parent
        $result = parent::open_multipart($action, $attr, $hidden);

        return $result;
    }

    /**
     * Generates a fieldset opening tag.
     *
     * @param   array   html attributes
     * @param   string  a string to be attached to the end of the attributes
     * @return  string
     */
    public static function open_fieldset($data = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $extra) = self::_addDefaults(__FUNCTION__, $data, $extra);

        // Call the parent
        $result = parent::open_fieldset($data, $extra);

        return $result;
    }

    /**
     * Generates a fieldset closing tag.
     *
     * @return  string
     */
    public static function close_fieldset()
    {
        // Call the parent
        $result = parent::close_fieldset();

        return $result;
    }

    /**
     * Generates a legend tag for use with a fieldset.
     *
     * @param   string  legend text
     * @param   array   HTML attributes
     * @param   string  a string to be attached to the end of the attributes
     * @return  string
     */
    public static function legend($text = '', $data = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($text, $data, $extra) = self::_addDefaults(__FUNCTION__, $text, $data, $extra);

        // Call the parent
        $result = '<span>' . $text . '</span>';
        
        return '<div'.form::attributes((array) $data).' '.$extra.'>'.$result.'</div>'."\n";
    }

    /**
     * Generates hidden form fields.
     * You can pass a simple key/value string or an associative array with multiple values.
     *
     * @param   string|array  input name (string) or key/value pairs (array)
     * @param   string        input value, if using an input name
     * @return  string
     */
    public static function hidden($data, $value = NULL)
    {
        if ( ! is_array($data))
        {
            $data = array
            (
                $data => $value
            );
        }

        $result = parent::open_fieldset(array('class' => 'hidden_inputs'), '');

        foreach ($data as $name => $value)
        {
            // This is a special case, we will get the classes back as well as
            // having to do this in a the parent loop, therefore we never
            // call the hidden parent but rather do the same function here...
            list($name, $value, $classes) = self::_addDefaults(__FUNCTION__, $name, $value);

            $attr = array
            (
                'type'  => 'hidden',
                'name'  => $name,
                'value' => $value,
                'class' => $classes
            );

            $result .= parent::input($attr)."\n";
        }

        $result .= self::close_fieldset();
        
        return $result;
    }

    /**
     * Creates an HTML form input tag. Defaults to a text type.
     * The default behavior is replicated here because we need to disable specialchars which
     * would only apply if we were not using doctrine
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @param   boolean       encode existing entities
     * @return  string
     */
    public static function input($data, $value = NULL, $extra = '', $double_encode = TRUE )
    {
        if (empty($data['type']) || $data['type'] != 'hidden')
        {
            // Add the Bluebox defaults (such as css classes)
            list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);
        }
        
        // Type and value are required attributes
        $data += array
        (
            'type'  => 'text',
            'value' => $value
        );

        return '<input'.form::attributes($data).' '.$extra.' />';
    }

    /**
     * Creates a HTML form password input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function password($data, $value = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);

        // Call the parent
        $result = parent::password($data, $value, $extra);

        return $result;
    }

    /**
     * Creates an HTML form upload input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function upload($data, $value = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);

        // Call the parent
        $result = parent::upload($data, $value, $extra);

        return $result;
    }

    /**
     * Creates an HTML form textarea tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @param   boolean       encode existing entities
     * @return  string
     */
    public static function textarea($data, $value = NULL, $extra = '', $double_encode = TRUE )
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);

        // Call the parent
        $result = parent::textarea($data, $value, $extra, $double_encode);

        return $result;
    }

    /**
     * Creates an HTML form select tag, or "dropdown menu".
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   array         select options, when using a name
     * @param   string        option key that should be selected by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function dropdown($data, $options = NULL, $selected = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $options, $selected, $extra) = self::_addDefaults(__FUNCTION__, $data, $options, $selected, $extra);

        if (!empty($data['translate']))
        {
            foreach ($options as $key => $value)
            {
                $options[$key] = self::_i18n($value);
            }
        }
        
        if (isset($data['translate']))
        {
            unset($data['translate']);
        }
        
        // Call the parent
        $result = parent::dropdown($data, $options, $selected, $extra);

        return $result;
    }

    /**
     * Creates an HTML form checkbox input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   boolean       make the checkbox checked by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function checkbox($data, $value = NULL, $checked = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $checked, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $checked, $extra);

        // Call the parent
        $result = self::hidden('__' .$data['name'], $data['unchecked']);
        
        unset($data['unchecked']);
        
        // Call the parent
        $result .= parent::checkbox($data, $value, $checked, $extra);

        return $result;
    }

    /**
     * Creates an HTML form radio input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   boolean       make the radio selected by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function radio($data = '', $value = NULL, $checked = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $checked, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $checked, $extra);

        // Call the parent
        $result = parent::radio($data, $value, $checked, $extra);

        
        return $result;
    }

    /**
     * Creates an HTML form submit input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function submit($data = '', $value = '', $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);

        // Call the parent
        $result = parent::submit($data, $value, $extra);

        return $result;
    }

    /**
     * Creates an HTML form button input tag.
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        input value, when using a name
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function button($data = '', $value = '', $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $value, $extra) = self::_addDefaults(__FUNCTION__, $data, $value, $extra);

        // Call the parent
        $result = parent::button($data, $value, $extra);
        
        return $result;
    }

    /**
     * Closes an open form tag.
     *
     * @param   string  string to be attached after the closing tag
     * @return  string
     */
    public static function close($extra = '')
    {
        list($extra) = self::_addDefaults(__FUNCTION__, $extra);
        
        // Call the parent
        $result = parent::close($extra);

        return $result;
    }

    /**
     * Creates an HTML form label tag.
     * The data field can contain a string indicating what field this label belongs to, or it can be an array
     * with the following keys possible:
     *   for   - The name of what field this label is for. Should match the fieldname of the following input box/checkbox/etc.
     *   issue - An error message/issue with the field to display (overrides hint), or if a boolean FALSE will suppress an error
     *   hint  - A small text string to show, in addition to the label
     *   help  - A help string to show. Based on the skin used, this is usually shown as a pop-up
     * In the above three data parameters, you can also pass the key in with a value that is an array containing additional attributes
     *
     * @param   string|array  label "for" name or an array of HTML attributes
     * @param   string        label text or HTML
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function label($data = '', $text = NULL, $extra = '')
    {
        // Add the Bluebox defaults (such as css classes)
        list($data, $text, $extra) = self::_addDefaults(__FUNCTION__, $data, $text, $extra);
        
        if (!empty($data['class']))
        {
            $baseClasses = $data['class'];
        } 
        else
        {
            $baseClasses = '';
        }


        if (!empty($data['help']))
        {
            // If only provided with a hint string make it into an array
            if (!is_array($data['help']))
            {
                $data['help'] = array('value' => $data['help']);
            }

            // Load ID and class attributes if they are not already populated
            $data['help'] += array
            (
                'id'  => 'help_' .$data['id'],
                'class' => $baseClasses.' help'
            );

            // Pass the hint text to i18n()
            $value = self::_i18n($data['help']['value']);

            unset($data['help']['value']);
            
            unset($data['help']['url']);

            // Create the help element
            $text .= '<span'.html::attributes($data['help']).' tooltip="'.$value.'">&nbsp;</span>';

            $data = arr::update($data, 'class', ' has_help');
        }

        if (!isset($data['issue']) || (isset($data['issue']) && $data['issue'] !== false))
        {
            if (empty($data['issue']['value']))
            {
                $data['issue']['value'] = self::_getError($data['for']);
            }

            if (!empty($data['issue']['value']))
            {
                // Load ID and class attributes if they are not already populated
                $data['issue'] += array
                (
                    'id'  => 'issue_' . $data['id'],
                    'class' => $baseClasses .' issue'
                );

                $value = self::_i18n($data['issue']['value']);
                
                unset($data['issue']['value']);

                // Create the error element
                $text .= '<span'.html::attributes($data['issue']).' >'.$value.'</span>';

                $has_error = TRUE;
            }
        } 
        
        if (empty($has_error) && !empty($data['hint']))
        {
            // If only provided with a hint string make it into an array
            if (!is_array($data['hint']))
            {
                $data['hint'] = array('value' => $data['hint']);
            }

            // Load ID and class attributes if they are not already populated
            $data['hint'] += array
            (
                'id'  => 'hint_' .$data['id'],
                'class' => $baseClasses .' hint'
            );

            // Pass the hint text to i18n()
            $value = self::_i18n($data['hint']['value']);
            
            unset($data['hint']['value']);

            // Create the hint element
            $text .= '<span'.html::attributes($data['hint']).' >'.$value.'</span>';

            $data = arr::update($data, 'class', ' has_hint');
        }

        // Remove this element from the lable attributes
        if(isset($data['issue']))
        {
            unset($data['issue']);
        }

        // Remove this element from the lable attributes
        if(isset($data['hint']))
        {
            unset($data['hint']);
        }

        // Remove this element from the lable attributes
        if(isset($data['help']))
        {
            unset($data['help']);
        }

        $data['for'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['for']), '_');

        // Call the parent
        $result = parent::label($data, $text, $extra);

        return $result;
    }

    public function open_section($title)
    {
        return self::open_fieldset() . self::legend($title);
    }

    public function close_section()
    {
        return self::close_fieldset();
    }
    
    /**
    * Creates an HTML form select tag with all avaliable timezones
    * Mash-up of kohana dropdowns and code retrieved from:
    * http://usphp.com/manual/en/function.timezone-identifiers-list.php on 7/23/2009
    * Modified by K Anderson
    *
    * TODO: This needs to be refactored to use the kohana html helpers
    *
    * @param   string|array  input name or an array of HTML attributes
    * @param   string        option key that should be selected by default
    * @param   string        a string to be attached to the end of the attributes
    * @return  string
    */
    public static function timezones($data, $selected = NULL, $extra = '')
    {
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        } 
        else
        {
            if (isset($data['options']))
            {
                // Use data options
                $options = $data['options'];
            }

            if (isset($data['selected']))
            {
                // Use data selected
                $selected = $data['selected'];
            }
        }

        $input = '<select'.form::attributes($data, 'select').' '.$extra.'>'."\n";

        function timezonechoice($selectedzone)
        {
            $all = timezone_identifiers_list();

            $i = 0;

            foreach($all AS $zone)
            {
                $zone = explode('/',$zone);

                $zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';

                $zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';

                $zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';

                $i++;
            }

            asort($zonen);

            $structure = '';

            foreach($zonen AS $zone)
            {
                extract($zone);

                if($continent == 'Africa' || $continent == 'America' || $continent == 'Antarctica' || $continent == 'Arctic' || $continent == 'Asia' || $continent == 'Atlantic' || $continent == 'Australia' || $continent == 'Europe' || $continent == 'Indian' || $continent == 'Pacific')
                {
                    if(!isset($selectcontinent))
                    {
                        $structure .= '<optgroup label="'.$continent.'">'; 
                    } 
                    elseif($selectcontinent != $continent)
                    {
                        $structure .= '</optgroup><optgroup label="'.$continent.'">';
                    }

                    if(isset($city) != '')
                    {
                        if (!empty($subcity) != '')
                        {
                            $city = $city . '/'. $subcity;
                        }
                        
                        $structure .= "<option ".((($continent.'/'.$city)==$selectedzone)?'selected="selected "':'')." value=\"".($continent.'/'.$city)."\">".str_replace('_',' ',$city)."</option>";
                    } 
                    else
                    {
                        if (!empty($subcity) != '')
                        {
                            $city = $city . '/'. $subcity;
                        }
                        
                        $structure .= "<option ".(($continent==$selectedzone)?'selected="selected "':'')." value=\"".$continent."\">".$continent."</option>";
                    }

                    $selectcontinent = $continent;
                }
            }

            $structure .= '</optgroup>';

            return $structure;
        }

        if (empty($selected))
        {
            $selectedzone = date_default_timezone_get();
        }
        else
        {
            $selectedzone = $selected;
        }

        $input .= timezonechoice($selectedzone);
        
        $input .= '</select>';

        return $input;
    }

    protected static function _addDefaults()
    {
        $args = func_get_args();

        $formElement = array_shift($args);
        
        // create a string of all default classes, with special cases
        if ($formElement == 'open_fieldset')
        {
            $defaultClasses = "fieldset";
        } 
        else
        {
            $defaultClasses = "$formElement";
        }
        
        // all skins and other things to append custom classes
        if (array_key_exists($formElement, self::$customClasses))
        {
            $custom = (array)self::$customClasses[$formElement];
            
            $defaultClasses .= ' ' . implode(' ', $custom);
        } 
        else if(array_key_exists('all', self::$customClasses))
        {
            $custom = (array)self::$customClasses['all'];
            
            $defaultClasses .= ' ' . implode(' ', $custom);
        }
        
        $defaultClasses = ' ' . strtolower($defaultClasses);

        // run the appopriate logic based on the form element
        switch ($formElement)
        {
            case 'open':
            case 'open_multipart':
                // create pointers to the parameters
                $action = &$args[0];

                $attr = &$args[1];

                $hidden = &$args[2];

                // if attr is not an array then make it one!
                if ( ! is_array($attr) || empty($attr))
                {
                    // this may confuse people who are loosing the stuff so log
                    if (is_string($attr))
                    {
                        kohana::log('error', 'The second argument to form::' . $formElement . ' must be an array! OVERWRITING!');
                    }
                    
                    $attr = array();
                }

                if (empty($attr['id']))
                {
                    $attr['id'] = trim(preg_replace('/[^a-zA-Z_{}]+/imx', '_', url::current()), '_');
                }

                // set a default hidden field with the forms name
                if (!is_array($hidden))
                {
                    $hidden = array();
                }
                
                $hidden += array(
                    'bluebox_form_name' => $attr['id']
                );

                // special cases for the forms
                if ($formElement == 'open')
                {
                    $defaultClasses = " form";
                } 
                else if ($formElement == 'open_multipart')
                {
                    $defaultClasses = " form multipart";
                }

                // Append the classes
                $attr = arr::update($attr, 'class', $defaultClasses);
                
                break;

            case 'close':
                // create pointers to the parameters
                $extra = &$args[0];

                break;

            case 'label':
                // create pointers to the parameters
                $data = &$args[0];

                $text = &$args[1];

                $extra = &$args[2];

                // standardize the $data var
                if ( ! is_array($data))
                {
                    if (is_string($data))
                    {
                        // Specify the input this label is for
                        $data = array('for' => $data);
                    }
                    else
                    {
                        // No input specified
                        $data = array();
                    }
                }
                
                if (!isset($data['for']))
                {
                    break;
                }

                // If the element does not have an id then generate one for it
                if (!empty($data['for']) && empty($data['id']))
                {
                    $data['id'] = 'label_' . trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['for']), '_');
                }

                // If the element this label belongs to has an error append a
                // has_error class to it
                if (!empty($data['for']) && self::_getError($data['for']))
                {
                    $defaultClasses .= ' has_error';
                }

                $text = self::_i18n($text);

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            case 'legend':
                // create pointers to the parameters
                $text = &$args[0];

                $data = &$args[1];

                $extra = &$args[2];

                // standardize the $data var
                $data = (array)$data;

                // if we have enough info to make an id and there is none do so
                if (!empty($text) && empty($data['id']))
                {
                    $data['id'] = 'legend_' . trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', strtolower($text)), '_');
                }

                $text = self::_i18n($text);

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            case 'open_fieldset':
                // create pointers to the parameters
                $data = &$args[0];
            
                $extra = &$args[1];

                // standardize the $data var
                $data = (array)$data;

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            case 'close_fieldset':
                break;

            case 'hidden':
                // create pointers to the parameters
                $name = &$args[0];

                $value = &$args[1];

                // check if we should attempt to fill the value
                $value = self::_attemptRePopulate($name, $value);

                // hidden fields dont have classes coming in, but expect them
                array_push($args, $defaultClasses);
                
                break;

            case 'input':
            case 'password':
            case 'upload':
            case 'textarea':
                // create pointers to the parameters
                $data = &$args[0];

                $value = &$args[1];

                $extra = &$args[2];

                // standardize the $data var
                if ( ! is_array($data))
                {
                    $data = array('name' => $data);
                }

                if (!isset($data['name']))
                {
                    break;
                }

                // If the element does not have an id then generate one for it
                if (empty($data['id']))
                {
                    $data['id'] = $data['name'];
                }
                
                $data['id'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['id']), '_');

                if ($formElement != 'button' && $formElement != 'submit')
                {
                    // check if we should attempt to fill the value
                    $value = self::_attemptRePopulate($data['name'], $value);

                    // If this field has an error append the has_error class...
                    if(self::_getError($data['name']))
                    {
                        $defaultClasses .= ' has_error';
                    }
                }

                if ($formElement == 'textarea') 
                {
                    if (empty($data['rows']))
                    {
                        $data['rows'] = '2';
                    }

                    if (empty($data['cols']))
                    {
                        $data['cols'] = '20';
                    }
                }

                // Some elements reuses form::input(), so dont re-append a new set of classes
                if (!empty($data['class']) && strstr($data['class'], $defaultClasses))
                {
                    break;
                }

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            case 'submit':
            case 'button':
                // create pointers to the parameters
                $data = &$args[0];

                $value = &$args[1];

                $extra = &$args[2];

                // standardize the $data var
                if ( ! is_array($data))
                {
                    $data = array('name' => $data);
                }
                
                if (!isset($data['name']))
                {
                    break;
                }
                
                // If the element does not have an id then generate one for it
                if (empty($data['id']))
                {
                     $data['id'] = $data['name'] . '_' . $value;
                }

                $data['id'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['id']), '_');

                if ($formElement == 'button' && empty($data['value']))
                {
                    $data['value'] = strtolower($value);
                }
                
                if ($formElement == 'button' || $formElement == 'submit')
                {
                    $value = self::_i18n($value);
                }

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            case 'dropdown':
                // create pointers to the parameters
                $data = &$args[0];

                $options = &$args[1];

                $selected = &$args[2];
                
                $extra = &$args[3];

                // standardize the $data var
                if ( ! is_array($data))
                {
                    $data = array('name' => $data);
                }
                
                if (!isset($data['name']))
                {
                    break;
                }

                // check if we should attempt to fill the selected
                $selected = self::_attemptRePopulate($data['name'], $selected);

                // If the element does not have an id then generate one for it
                if (empty($data['id']))
                {
                    $data['id'] = $data['name'];
                }

                $data['id'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['id']), '_');

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);
                
                break;

            // data, value, checked, extra
            case 'checkbox':
                // create pointers to the parameters
                $data = &$args[0];

                $value = &$args[1];

                $checked = &$args[2];

                $extra = &$args[3];

                // if there is no default value then use bool true
                if (is_null($value))
                {
                    $value = TRUE;
                }

                // standardize the $data var
                if ( ! is_array($data))
                {
                    $data = array('name' => $data);
                }

                if (!isset($data['name']))
                {
                    break;
                }

                // check if we should attempt to fill checked
                $checked = self::_attemptRePopulate($data['name'], $checked, $value);
                
                // see if we have a unchecked value or can quess it
                if (!isset($data['unchecked']))
                {
                    if (is_bool($value))
                    {
                        if ($value)
                        {
                            $data['unchecked'] = 0;
                        }
                        else
                        {
                            $data['unchecked'] = 1;
                        }
                    } 
                    else
                    {
                        $data['unchecked'] = 0;
                    }
                }

                // If the element does not have an id then generate one for it
                if (empty($data['id']))
                {
                    $data['id'] = $data['name'];
                }
                
                $data['id'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['id']), '_');
                
                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);

                break;

            case 'radio':
                // create pointers to the parameters
                $data = &$args[0];

                $value = &$args[1];

                $checked = &$args[2];

                $extra = &$args[3];

                // standardize the $data var
                if ( ! is_array($data))
                {
                    $data = array('name' => $data);
                }

                if (!isset($data['name']))
                {
                    break;
                }

                if (is_null($checked))
                {
                    // check if we should attempt to fill checked
                    $repopulate = self::_attemptRePopulate($data['name'], $checked, $value);

                    $checked = FALSE;

                    if ($repopulate == $value)
                    {
                        $checked = TRUE;
                    }
                }

                // If the element does not have an id then generate one for it
                if (empty($data['id']))
                {
                    $data['id'] = $data['name'];
                }

                $data['id'] = trim(preg_replace('/[^a-zA-Z0-9_{}]+/imx', '_', $data['id']), '_');

                // Append the classes
                $data = arr::update($data, 'class', $defaultClasses);

                break;
        }
        
        return $args;
    }

    /**
     * This function attempts to find a repopulate var based on the name of the element and
     * set the value, unless that value is not already set.
     *
     * @param array $data The elements data array, used to check if the key value exists
     * @param string $value The current elements value, this is set if it is currently empty
     * @return void
     */
    protected function _attemptRePopulate($name, $value, $returnValue = NULL)
    {
        if (!is_null($value))
        {
            return $value;
        }

        if (empty($name) || !is_string($name))
        {
            return $value;
        }

        if (substr($name, 0, 2) == '__')
        {
            return $value;
        }

        if ((!is_null($returnValue)) AND ($pos = strpos($name, '[]')))
        {
            $parentArray = substr($name, 0, $pos);

            $subArray = substr($name, $pos + 2);

            foreach (self::$repopulateValues as $key => $repopulateValue)
            {
                if (!strstr($key, $parentArray))
                {
                    continue;
                }

                if (!strstr($key, $subArray))
                {
                    continue;
                }

                if ($returnValue == $repopulateValue)
                {
                    return TRUE;
                }
            }
        }

        // see if we have a repopulate value in our cache for this name
        if (array_key_exists($name, self::$repopulateValues))
        {
            return self::$repopulateValues[$name];
        }

        if (!is_object (View::$instance))
        {
            return $value;
        }

        // get the first part of the name (up to the first [)
        list($baseName) = explode('[', $name);

        if (!isset(View::$instance->$baseName))
        {
            return $value;
        }

        // setup a pointer to the views variable (ie View::$instanace->Devices)
        $document = &View::$instance->$baseName;

        if ($document instanceof Bluebox_Record)
        {
            $document = $document->toArray();
        }

        if (!is_array($document))
        {
            return $document;
        }

        // merge the values we just found into our cache (after
        // flattening the array)
        $repopulateValues = array($baseName => $document);
        
        self::$repopulateValues = arr::merge_recursive_distinct(
            self::$repopulateValues,
            arr::flatten($repopulateValues)
        );

        if ((!is_null($returnValue)) AND ($pos = strpos($name, '[]')))
        {
            $parentArray = substr($name, 0, $pos);

            $subArray = substr($name, $pos + 2);

            foreach (self::$repopulateValues as $key => $repopulateValue)
            {
                if (!strstr($key, $parentArray))
                {
                    continue;
                }

                if (!strstr($key, $subArray))
                {
                    continue;
                }

                if ($returnValue == $repopulateValue)
                {
                    return TRUE;
                }
            }
        }

        if (array_key_exists($name, self::$repopulateValues))
        {
            return self::$repopulateValues[$name];
        }

        return $value;
    }

    /**
     * This function will attempt to find an error messsage for the specified field
     *
     * @param string $field This is the field name that we want to find an error for
     * @return string|false This will either return an error string or bool false if non found
     */
    protected function _getError($field)
    {
        // No point in processing an empty $field
        if (empty($field))
        {
            return FALSE;
        }
        
        $errors = Bluebox_Controller::$validation->errors();
        
        if (array_key_exists($field, $errors))
        {
            return $errors[$field];
        }

        return FALSE;
    }

    /**
     * This function will guess the i18n keys that are most likely or convert an i18n
     * key if provided
     *
     * @param string $field This is the name of the field to look for
     * @param string $value This is the current value of the element
     * @return bool This will return true if i18n was sucessfull, otherwise false
     */
    protected function _i18n($value)
    {
        return $value;
    }
}