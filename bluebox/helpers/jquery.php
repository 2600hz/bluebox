<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * jquery helper
 * retrieved from http://jquery.hohli.com/ and modified by K Anderson on 06 05 2009
 *
 * @author Anton Shevchuk
 * @author Karl Anderson
 * @license LGPL
 * @access   public
 * @package  jquery
 * @version  0.8
 */
class jquery_Core
{
    /**
     * static var for singlton
     *
     * @var jquery_Core
     */
    public static $jquery;
    /**
     * response stack
     * @var array
     */
    public $response = array(
        // actions (addMessage, addError, eval etc.)
        'a' => array() ,
        // jqueries
        'q' => array()
    );
    /**
     * list of jquery plugins to load
     *
     * @var array
     */
    public $plugins = array();
    /**
     * List of arbitrary JS scripts to load after jQuery is available/loaded
     * @var array
     */
    public $extraScripts = array();
    /**
     * List of extra CSS stylesheets to load
     * @var array
     */
    public $extraCss = array();
    /**
     * a collection of dependencies mappings fo jquery plugins (loaded in order)
     *
     * @var array
     * @access private
     */
    public static $jqPlugins = array(
        'tabs' => array(
            'js' => array(
                'ui.core.js',
                'ui.tabs.js'
            ) ,
            'css' => 'ui.all.css'
        ) ,
        'accordion' => array(
            'js' => array(
                'ui.core.js',
                'ui.accordion.js'
            ) ,
            'css' => 'ui.all.css'
        ) ,
        'grid' => array(
            'js' => array(
                'jqGrid/jquery.jqGrid.js',
                'jqGrid/js/jqDnR.js'
            ) ,
            'css' => array(
                'jqGrid/jqModal.css',
                'jqGrid/steel/grid.css'
            )
        ) ,
        'betagrid' => array(
            'js' => array(
                'jqGrid/i18n/grid.locale-en.js',
                'jqGrid/jquery.jqGrid.min.js'
            ) ,
            'css' => array(
                'ui.all.css',
                'jqGrid/ui.jqgrid.css',
                'jqGrid/jquery.searchFilter.css'
            )
        ) ,
        'dialog' => array(
            'js' => array(
                'ui.core.js',
                'ui.dialog.js',
                'ui.draggable.js',
                'ui.resizable.js'
            ) ,
            'css' => 'ui.all.css'
        ) ,
        'selectable' => array(
            'js' => array(
                'ui.core.js',
                'ui.selectable.js'
            ) ,
            'css' => 'ui.all.css'
        ) ,
        'growl' => array(
            'js' => 'jquery.jgrowl.js',
            'css' => 'jquery/jquery.jgrowl.css'
        ) ,
        'vchecks' => array(
            'js' => 'jquery.vchecks.js',
            'css' => 'jquery/geogoer.vchecks.css'
        ) ,
        'autocomplete' => array(
            'js' => 'jquery.autocomplete.js',
            'css' => 'jquery/jquery.autocomplete.css'
        ) ,
        'dropdowns' => array(
            'js' => array(
                'ui.core.js',
                'ui.dropdownchecklist.js'
            ) ,
            'css' => 'jquery/ui.dropdownchecklist.css'
        ) ,
        'form' => array(
            'js' => 'jquery.form.js',
        ) ,
        'dependent' => array(
            'js' => 'jquery.dependent.js'
        ) ,
        'blockUI' => array(
            'js' => 'jquery.blockUI.js'
        ) ,
        'selectbox' => array(
            'js' => 'jquery.selectboxes.js'
        ) ,
        'scrollTo' => array(
            'js' => 'jquery.scrollTo-1.4.2-min.js'
        ) ,
        'datepicker' => array(
            'js' => array(
                'ui.core.js',
                'ui.datepicker.js'
            ) ,
            'css' => 'ui.all.css',
        ) ,
        'thickbox' => array(
            'js' => 'thickbox.js',
            'css' => 'jquery/thickbox.css'
        ) ,
        'qtip' => array(
            'js' => 'jquery.qtip.js'
        ) ,
        'dragdrop' => array(
            'js' => array(
                'ui.core.js',
                'ui.draggable.js',
                'ui.droppable.js'
            )
        ) ,
        'sortable' => array(
            'js' => array(
                'ui.core.js',
                'ui.sortable.js'
            )
        ) ,
        'multiselect' => array(
            'js' => array(
                'jquery.tmpl.1.1.1.js',
                'jquery.scrollTo-1.4.2-min.js',
                'jquery.blockUI.js',
                'ui.core.js',
                'ui.draggable.js',
                'ui.sortable.js',
                'ui.droppable.js',
                'ui.multiselect.js'
            ) ,
            'css' => 'jquery/ui.multiselect.css'
        ) ,
        'treeview' => array(
            'js' => array(
                'jquery.treeview.js',
                'jquery.treeview.async.js'
            ),
            'css' => 'jquery/treeview/jquery.treeview.css'
        ) ,
        'json' => array(
            'js' => 'jquery.json.js'
        ),
        'asmselect' => array(
            'js' => array('ui.core.js', 'jquery.asmselect.js'),
            'css' => 'jquery/jquery.asmselect.css'
        ),
        'cookie' => array(
            'js' => 'jquery.cookie.js'
        ),
        'ipaddress' => array(
            'js' => array('jquery.caret.js', 'jquery.ipaddress.js'),
            'css' => 'jquery/jquery.ipaddress.css'
        ),
        'persistent' => array(
            'js' => 'persist-min.js'
        ),
        'infiniteCarousel' => array(
            'js' => 'infiniteCarousel.js'
        ),
        'destinations' => array(
            'js' => '/destinations.js'
        )
    );
    /**
     * the jquery skin to use, see assets/css/jquery for options
     *
     * @var array
     */
    public static $skinName = 'smoothness';
    /**
     * __construct
     *
     * @access  public
     */
    public function __construct()
    {
        // Populate $jqPlugins array here to make jquery plugins dynamic
        // Populate $plugins here to have a plugin always avaliable then
        // call init so singleton is populated causing assests to load!
        
    }
    /**
     * init
     * implement a singleton, create if needed
     *
     * @return void
     */
    public static function init()
    {
        if (empty(jquery::$jquery)) {
            jquery::$jquery = new jquery();
        }
        return true;
    }
    /**
     * addData
     *
     * add any data to response
     *
     * @param string $key
     * @param mixed $value
     * @param string $callBack
     * @return jquery
     */
    public static function addData($key, $value, $callBack = null)
    {
        jquery::init();
        $jqueryAction = new jqueryAction();
        $jqueryAction->add('k', $key);
        $jqueryAction->add('v', $value);
        // add call back func into response JSON obj
        if ($callBack) {
            $jqueryAction->add("callback", $callBack);
        }
        jquery::addAction(__FUNCTION__, $jqueryAction);
        return jquery::$jquery;
    }
    /**
     * addMessage
     *
     * @param string $msg
     * @param string $callBack
     * @param array  $params
     * @return jquery
     */
    public static function addMessage($msg, $callBack = null, $params = null)
    {
        jquery::init();
        $jqueryAction = new jqueryAction();
        $jqueryAction->add("msg", $msg);
        // add call back func into response JSON obj
        if ($callBack) {
            $jqueryAction->add("callback", $callBack);
        }
        if ($params) {
            $jqueryAction->add("params", $params);
        }
        jquery::addAction(__FUNCTION__, $jqueryAction);
        return jquery::$jquery;
    }
    /**
     * addError
     *
     * @param string $msg
     * @param string $callBack
     * @param array  $params
     * @return jquery
     */
    public static function addError($msg, $callBack = null, $params = null)
    {
        jquery::init();
        $jqueryAction = new jqueryAction();
        $jqueryAction->add("msg", $msg);
        // add call back func into response JSON obj
        if ($callBack) {
            $jqueryAction->add("callback", $callBack);
        }
        if ($params) {
            $jqueryAction->add("params", $params);
        }
        jquery::addAction(__FUNCTION__, $jqueryAction);
        return jquery::$jquery;
    }
    /**
     * evalScript
     *
     * @param  string $foo
     * @return jquery
     */
    public static function evalScript($foo)
    {
        jquery::init();
        $jqueryAction = new jqueryAction();
        $jqueryAction->add("foo", $foo);
        jquery::addAction(__FUNCTION__, $jqueryAction);
        return jquery::$jquery;
    }
    /**
     * Creates a JavaScript object literal that can be used
     * as a JSON object but also carry functions
     * Last modified by K Anderson 06-05-09
     *
     * @return string JSON
     */
    public static function getResponse()
    {
        jquery::init();
        // Create the json object
        $json = json_encode(jquery::$jquery->response);
        // Find function declarations and remove the quotes
        // (this effectively violates JSON but not JS object literals as a whole)
        // Regex break down at bottom of page....
        $json = preg_replace('/"(function\s*[^\s]{0,}\s*\((?:.*?))"/i', '${1}', $json);
        // Fix the escaped \, remove the translated newlines, and remove access whitespace
        $json = str_replace('\/', '/', $json);
        $json = str_replace('\n', '', $json);
        $json = str_replace('\r', '', $json);
        $json = preg_replace('/\s\s+/', ' ', $json);
        return $json;
    }
    /**
     * addQuery
     * add a jQuery selector to the stack and create a new query
     *
     * @return jqueryElement
     */
    public static function addQuery($selector)
    {
        jquery::init();
        return new jqueryElement($selector);
    }
    /**
     * addQuery
     * add a query to the stack
     *
     * @param  jqueryElement $jqueryElement
     * @return void
     */
    public static function addElement(jqueryElement & $jqueryElement)
    {
        jquery::init();
        array_push(jquery::$jquery->response['q'], $jqueryElement);
    }
    /**
     * addAction
     * add a jQuery action to the stack
     *
     * @param  string $name
     * @param  jqueryAction $jqueryAction
     * @return void
     */
    public static function addAction($name, jqueryAction & $jqueryAction)
    {
        jquery::init();
        jquery::$jquery->response['a'][$name][] = $jqueryAction;
    }
    /**
     * Set the jquery skin to use
     *
     * @param string skin_name
     * @return bool success
     * @author K Anderson
     */
    public static function setSkin($skinName = '')
    {
        if (!empty($skinName) && is_dir(getcwd() . '/assets/css/jquery/' . $skinName . '/')) {
            jquery::$skinName = $skinName;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get the jquery skin name
     *
     * @return string skinName
     * @author K Anderson
     */
    public static function getSkin()
    {
        return jquery::$skinName;
    }
    /**
     * addPlugin
     * This function adds a loadable jquery plugin and also
     * gets any helper depedency (either name or directory)
     *
     * @param string or array of $pluginNames
     * @return jquery
     * @author K Anderson
     */
    public static function addPlugin($pluginNames = array())
    {
        $jqPlugins = jquery::$jqPlugins;
        // Convert a string to array to standardize handling
        $pluginNames = is_string($pluginNames) ? array(
            $pluginNames
        ) : $pluginNames;
        foreach($pluginNames as $pluginName) {
            // Check if a proper pluginName has been specified
            if (array_key_exists($pluginName, $jqPlugins)) {
                jquery::init();
                // Put the plugin name into the load list
                jquery::$jquery->plugins[] = $pluginName;
            }
        }
        return jquery::$jquery;
    }
    /**
     * addLibrary
     * Add any sort of JS library that needs to be loaded after jquery is available. Optionally add css files, too
     *
     * @param string
     * @return jquery
     * @author Darren Schreiber
     */
    public static function addLibrary($name, $js_files, $css_files = array() , $module = NULL)
    {
        if (isset(self::$jqPlugins[$name])) {
            throw new Exception('jQuery library ' . $name . ' already exists! Can\'t add it again.');
        }
        self::$jqPlugins[$name] = array(
            'js' => (array)$js_files,
            'css' => (array)$css_files
        );
        if ($module) {
            self::$jqPlugins[$name]['module'] = $module;
        }
        return self::addPlugin($name);
    }
    /**
     * This function is used to populate the JSON response to
     * jquery.php.js
     *
     * @return void|string
     * @param bool $inline[optional] If false the json wrapped in a script tag is returned, otherwise it is echo'ed
     */
    public static function buildResponse($inline = true)
    {
        $script = '';
        if (!empty(jquery::$jquery)) {
            $script = javascript::codeBlock('    php.success(' . jquery::getResponse() . ', true);', array('inline' =>
                $inline
            ));
        }
        if ($inline) echo $script;
        else return $script;
    }
    public static function addJsAssets() {
        // Check if the jquery helper was initialized
        if (empty(jquery::$jquery))  return;

        $jqPlugins = jquery::$jqPlugins;

        // Ensure our core assest are on the page
        javascript::add('jquery/jquery-1.3.2.min.js', 20);
        javascript::add('jquery/jquery.helper.js', 20);

        // Load any additional jquery plugins
        foreach(jquery::$jquery->plugins as $plugin) {
            // See if there are js jqPlugins
            if (empty($jqPlugins[$plugin]['js'])) continue;

            $scripts = $jqPlugins[$plugin]['js'];
            foreach((array)$scripts as $js) {

                if (isset($jqPlugins[$plugin]['module'])) {
                    javascript::add($jqPlugins[$plugin]['module'] . '/' . $js);
                } else {
                    // If no directory is specified then assume assets/js/jquery
                    if (stripos($js, '/') === false) $js = 'jquery/' . $js;
                    javascript::add($js, 30);
                }
            }
        }
    }
    public static function addCssAssets() {
        // Check if the jquery helper was initialized
        if (empty(jquery::$jquery)) return;

        $jqPlugins = jquery::$jqPlugins;

        // Load any additional jquery plugins
        foreach(jquery::$jquery->plugins as $plugin) {
            // Check if we need a css tag for this asset
            if (empty($jqPlugins[$plugin]['css'])) continue;

            $stylesheets = $jqPlugins[$plugin]['css'];
            foreach((array)$stylesheets as $css) {

                // Is this a plugin that's part of a module? If so, assume all paths are already specified
                if (isset($jqPlugins[$plugin]['module'])) {
                    stylesheet::add($jqPlugins[$plugin]['module'] . '/' . $css, 20);
                } else {
                    // If no directory is specified then assume assets/css/jquery/{skinName}
                    if (stripos($css, '/') === false) $css = 'jquery/' . jquery::$skinName . '/' . $css;
                    stylesheet::add($css, 30);
                }
            }
        }
    }
}
/**
 *
 * Regex to extract Functions from JSON objects
 * By K Anderson version 1.2
 *
 * "(function\s*[^\s]{0,}\s*\((?:.*?))"
 *
 * Token Tree:
 *
 * Match the character `"` literally `"`
 * Match the regular expression below and capture its match into backreference number 1 `(function\s*[^\s]{0,}\s*\((?:.*?))`
 * Match the characters `function` literally `function`
 * Match a single character that is a `whitespace character` (spaces, tabs, line breaks, etc.) `\s*`
 *    Between zero and unlimited times, as many times as possible, giving back as needed (greedy) `*`
 * Match any character that is not a `Match a single character that is a `whitespace character` (spaces, tabs, line breaks, etc.)` `[^\s]{0,}`
 *    Between zero and unlimited times, as many times as possible, giving back as needed (greedy) `{0,}`
 * Match a single character that is a `whitespace character` (spaces, tabs, line breaks, etc.) `\s*`
 *    Between zero and unlimited times, as many times as possible, giving back as needed (greedy) `*`
 * Match the character `(` literally `\(`
 * Match the regular expression below `(?:.*?)`
 *    Match any single character that is not a line break character `.*?`
 *       Between zero and unlimited times, as few times as possible, expanding as needed (lazy) `*?`
 * Match the character `"` literally `"`
 *
 *
 * Basic match:
 *
 * "function {possible single word} ( {anything expanding as needed}"
 *
 *
 */
