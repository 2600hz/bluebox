<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Javascript
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class javascript
{
    public static $cache = FALSE;

    protected static $jstags = array(
        'codeblock' => '<script type="text/javascript">%s</script>',
        'domready' => '$(document).ready(function () {%s});',
        'ajaxready' => '$(document).bind(\'ajaxStop.jqueryGetResponse.javascriptHelper\', function(){ %s $(document).unbind(\'ajaxStop.jqueryGetResponse.javascriptHelper\'); });'
    );

    protected static $blockOptions = array();

    protected static $outputBuffer = NULL;

    protected static $scriptPaths = array();

    protected static $scriptBlocks = array();

    public static function codeBlock($script = NULL, $options = array())
    {
        // if options is not an array, the consider it the domready bool
        if (!is_array($options)) {

            $options = array('domready' => (bool)$options);

        }

        // ensure our defualt options are populated
        $options += array(
            'safe' => TRUE,
            'minify' => TRUE,
            'inline' => FALSE,
            'domready' => TRUE,
            'weight' => 40,
            'scriptname' => NULL
        );

        extract($options);


        // if script is empty then they are starting a new code block
        if (empty($script)) {

            // mark that the script is in the output buffer
            $options['inBlock'] = TRUE;

            // store what is in the buffer right now
            self::$outputBuffer = @ob_get_contents();

            // store the options
            self::$blockOptions = $options;

            // clean out the buffer
            @ob_end_clean();

            // start a new buffer so we can build our code block in it
            ob_start();

            return NULL;

        }

        // determine how we are outputting the script
        if ($inline) {

            // if we want to minify this do so
            if ($minify) {

                $script = self::minify($script);

            }

            // if we are wrapping the script in a dom ready tag
            if ($domready) {

                // choose the appropriate tag based on the request type
                if (request::is_ajax()) {

                    $script = sprintf(self::$jstags['ajaxready'], "\n" .$script ."\n");

                } else {

                    $script = sprintf(self::$jstags['domready'], "\n" .$script ."\n");

                }

            }

            // if we are making the script HTML safe
            if ($safe) {

                $script  = "\n" .'//<![CDATA[' ."\n" .$script ."\n" .'//]]>' ."\n";

            }

            return sprintf(self::$jstags['codeblock'], $script) ."\n";

        } else {

            if (self::$cache) {

                $cache = Cache::instance();

                if (empty($scriptname)) {

                    $cacheName = 'js_'.sha1($script);

                } else {
                    
                    $cacheName = 'js_'.$scriptname;
                }

                // see if this script is already cached, if not do so
                $cached = $cache->get($cacheName);

                if (is_null($cached)) {

                    $cache->set($cacheName, $script, array(), Kohana::config('cache.default.lifetime'));

                }

                // only store the cache id
                $script = $cacheName;

            }

            // if we are wrapping the script in a dom ready tag
            if ($domready) {

                if (empty($scriptname)) {

                    self::$scriptBlocks['domready'][$weight][] = $script;
                    
                } else {

                    self::$scriptBlocks['domready'][$weight][$scriptname] = $script;
                    
                }

            } else {

                if (empty($scriptname)) {

                    self::$scriptBlocks['codeblocks'][$weight][] = $script;

                } else {

                    self::$scriptBlocks['codeblocks'][$weight][$scriptname] = $script;
                    
                }

            }

        }

    }

    public static function blockEnd()
    {
        // see if we are being called with an open style output buffer
        if (empty(self::$blockOptions['inBlock'])) {

                return;

        }
        
        // get the scrip out of the buffer
        $script = @ob_get_contents();

        // clean and restart the buffer
        @ob_end_clean();

        ob_start();

        echo self::$outputBuffer;

        // null our temporary storage
        self::$outputBuffer = NULL;

        // get the options
        $options = self::$blockOptions;

        self::$blockOptions = array();

        // if we didnt recieve anything then move on
        if (empty($script)) {

            return;

        }

        // process what we got in the style buffer
        echo self::codeBlock($script, $options);
    }

    public static function add($paths, $options = array(), $directory = 'assets/js')
    {
        // if they didnt give us anything to include move on
        if (empty($paths)) return FALSE;

        // if options is not an array assume it is a weight
        if (!is_array($options)) {

            $options = array('weight' => (int)$options);

        }

        // ensure our defualt options are populated
        $options += array(
            'safe' => TRUE,
            'minify' => TRUE,
            'inline' => FALSE,
            'asCodeBlock' => FALSE,
            'scan' => TRUE,
            'docroot' => DOCROOT,
            'weight' => 40
        );

        extract($options);

        // normalize the directory option
        if (!empty($directory)) {

            $directory = rtrim($directory, '/').'/';

        }

        // include each of the paths provided
        foreach ((array)$paths as $path) {

            // if scanning is not disabled then look for the css file
            if ($scan) {

                // the kohana find_file needs the extensio seperate from the path
                $extn = pathinfo($path, PATHINFO_EXTENSION);

                if (empty($extn)) {

                    $extn = 'js';

                } else {

                    $path = substr($path, 0, -1 * (strlen($extn) + 1));

                }

                // first check the modules for the assets
                $search = $directory .$path .'.' .$extn;

                if($found = Kohana::find_file(rtrim($directory, '/'), $path, FALSE,  $extn)) {

                    $path = str_replace($docroot, '', $found);

                // no? how about in the skin
                } else if (is_file($docroot .skins::getSkin() .$search)) {

                    $path = skins::getSkin() .$search;

                // still? ok what about in the core
                } else if (is_file($docroot .$search)) {

                    $path = $search;

                // hmm, ok well did they give us an absolute path?
                } else if (is_file($path .$extn)) {

                    $path = $path . $extn;

                } else {

                    // thats it, I looked everywhere
                    kohana::log('error', 'Unable to locate JS include ' .$path);

                    continue;

                }

            }

            // are we putting this file into a code block or inline?
            if ($inline || $asCodeBlock) {

                // get the file contents
                $script = @file_get_contents($path);

                if (empty($script)) {

                    kohana::log('error', 'Unable to read js file '. $path);

                    continue;

                }

                // make the file contents into a code block
                echo self::codeBlock($script, $options);

            } else {

                self::$scriptPaths[$weight][] = $path;

            }

        }

        // is_file caches info about the files we looked for, clear that now
        clearstatcache();

        return TRUE;

    }

    public static function renderCodeBlocks($inline = TRUE, $options = array())
    {
        // if options is not an array assume it is the minify option
        if (!is_array($options)) {

            $options = array('minify' => (bool)$options);

        }

        // ensure our defualt options are populated
        $options += array(
            'safe' => TRUE,
            'minify' => FALSE,
            'domReady' => TRUE,
            'immediate' => TRUE,
            'jquery' => TRUE
        );

        extract($options);

        $cache = Cache::instance();

        // have we been asked to build the jquery response
        // (which is a domready script)
        if ($domReady && $jquery && class_exists('jquery')) {

            jquery::buildResponse(FALSE);

        }

        // if we are rendering the domready code block on this pass
        if ($domReady && !empty(self::$scriptBlocks['domready'])) {

            // check if we are minifing the domready block
            if($minify === TRUE || ( is_array($minify) && in_array('domReady', $minify) ) ) {

                $minifyThis = TRUE;

            } else {

                $minifyThis = FALSE;

            }

            // build a var with the domready code blocks
            $domReadyBlocks = self::$scriptBlocks['domready'];

            self::$scriptBlocks['domready'] = array();

            // sort the domready codeblocks by weight
            ksort($domReadyBlocks);

            // loop all the dom ready blocks and determine what needs to go on
            // this page and in what order
            $domReadyBlock = array();

            foreach ($domReadyBlocks as $codeblocks) {

                // if we are storing these codeblocks in cache then get them now
                if (self::$cache) {

                    // since they are in cache we have to do each one
                    foreach ($codeblocks as $cacheName) {

                        // check if we are minify'n these script blocks
                        if ($minifyThis) {

                            // see if we have already minified this
                            $cacheMinName = 'js_min_' .substr($cacheName, 3);

                            $cached = $cache->get($cacheMinName);

                            // if this style is not already cached minified do so
                            if (is_null($cached)) {

                                // try to get the un-minified script
                                $cached = $cache->get($cacheName);

                                if (is_null($cached)) {

                                    kohana::log('error', 'Unable to minify cached codeblock  ' .$script);

                                    continue;

                                }

                                // minify and cache this script
                                $cached = self::minify($cached);

                                $cache->set($cacheMinName, $cached, array(), Kohana::config('cache.default.lifetime'));

                            }

                            // add the minified style to the output buffer
                            $domReadyBlock[] = $cached;

                        } else {

                            // see if we can get this script out of cache
                            $cached = $cache->get($cacheName);

                            if (is_null($cached)) {

                                kohana::log('error', 'Unable to locate cached codeblock ' .$script);

                                continue;

                            }

                            $domReadyBlock[] = $cached;

                        }

                    }

                } else {

                    // if we are not storing the script in cache then the array
                    // contains the code blocks directly
                    $domReadyBlock = array_merge($domReadyBlock, $codeblocks);

                }

            }

        }

        // if we are building the code blocks for immediate execution
        if ($immediate && !empty(self::$scriptBlocks['codeblocks'])) {

            // the immediate code block always starts with our urlbase var
            $immediateBlock = array( '    var URLBASE = \'' . url::base() . '\';');

            // check if we are minifing the immediate block
            if($minify === TRUE || ( is_array($minify) && in_array('immediate', $minify) ) ) {

                $minifyThis = TRUE;

            } else {

                $minifyThis = FALSE;

            }

            // build a var with the immediate code blocks
            $immediateBlocks = self::$scriptBlocks['codeblocks'];

            self::$scriptBlocks['codeblocks'] = array();

            // sort the immediate codeblocks by weight
            ksort($immediateBlocks);

            // loop all the immediate blocks and determine what needs to go on
            // this page and in what order
            foreach ($immediateBlocks as $codeblocks) {

                // if we are storing these codeblocks in cache then get them now
                if (self::$cache) {

                    // since they are in cache we have to do each one
                    foreach ($codeblocks as $cacheName) {

                        // check if we are minify'n these script blocks
                        if ($minifyThis) {

                            // see if we have already minified this
                            $cacheMinName = 'js_min_' .substr($cacheName, 3);

                            $cached = $cache->get($cacheMinName);

                            // if this style is not already cached minified do so
                            if (is_null($cached)) {

                                // try to get the un-minified script
                                $cached = $cache->get($cacheName);

                                if (is_null($cached)) {

                                    kohana::log('error', 'Unable to minify cached codeblock  ' .$script);

                                    continue;

                                }

                                // minify and cache this script
                                $cached = self::minify($cached);

                                $cache->set($cacheMinName, $cached, array(), Kohana::config('cache.default.lifetime'));

                            }

                            // add the minified style to the output buffer
                            $immediateBlock[] = $cached;

                        } else {

                            // see if we can get this script out of cache
                            $cached = $cache->get($cacheName);

                            if (is_null($cached)) {

                                kohana::log('error', 'Unable to locate cached codeblock ' .$script);

                                continue;

                            }

                            $immediateBlock[] = $cached;

                        }

                    }

                } else {

                    // if we are not storing the script in cache then the array
                    // contains the code blocks directly
                    $immediateBlock = array_merge($immediateBlock, $codeblocks);

                }

            }

        }

        $codeBlock = "\n";

        // if the immediate block is not empty add it to this code block
        if (!empty($immediateBlock)) {

            $codeBlock .= implode("\n", $immediateBlock);

        }

        // if the dom ready block is not empty add it to this code block
        if (!empty($domReadyBlock)) {

            $domReadyBlock = implode("\n", $domReadyBlock);

            // choose the appropriate tag based on the request type
            if (request::is_ajax()) {

                $codeBlock .= sprintf(self::$jstags['ajaxready'], "\n" . $domReadyBlock . "\n");

            } else {

                $codeBlock .= sprintf(self::$jstags['domready'], "\n" . $domReadyBlock . "\n");

            }

        }

        if ($inline) {

            if ($codeBlock == "\n") return;

            // if we are making this script HTML safe
            if ($safe) {

                $codeBlock = "\n" .'//<![CDATA[' . $codeBlock ."\n" .'//]]>' ."\n";

            }

            echo sprintf(self::$jstags['codeblock'], $codeBlock) ."\n";

        } else {

            if ($codeBlock == "\n") return;

            // if we are making this script HTML safe
            if ($safe) {

                $codeBlock = "\n" .'//<![CDATA[' .$codeBlock ."\n" .'//]]>' ."\n";

            }

            return sprintf(self::$jstags['codeblock'], $codeBlock) ."\n";

        }
        
    }

    public static function renderLinks($inline = FALSE, $index = FALSE)
    {
        // build a var with the included css files
        $scriptincludes = self::$scriptPaths;

        self::$scriptPaths = array();

        // Get the session so we can track what assets are on the page
        $session = Session::instance();

        // If this is a full page load (ie not ajax) then there are no assest already on page
        if (!request::is_ajax()) $session->delete('javascript.onPageAssets');

        // Initialize the vars to track what has been added, and convience wrappers
        $onPageAssets = $session->get('javascript.onPageAssets', array());

        // sort the script include paths by weight
        ksort($scriptincludes);

        // loop all the script includes and determine what needs to go on this page
        // and in what order
        $includes = array();

        foreach ($scriptincludes as $scripts) {

            // make sure there are no duplicates in this array
            $scripts = array_unique($scripts);

            // make sure these css links are not already on the page
            // via ajax/parent or in another weight category (lowest weight wins)
            $scripts = array_diff($scripts, $onPageAssets);

            // add the filtered list to the array of assets to put on the page
            $includes = array_merge($includes, $scripts);

            // keep track of what we add to the pages
            $onPageAssets = array_merge($onPageAssets, $scripts);

        }

        // NOTICE: setting $session here causes segfault during ajax
        // made a hack to move this set outside the event .....
        Bluebox_Controller::$onPageAssets['js'] = $onPageAssets;
        //$session->set('javascript.onPageAssets', $onPageAssets);

        // create a list of links from the script includes
        $linkList = html::script($includes, $index);

        // are we displaying this inline or returning the string
        if ($inline) {

            echo $linkList;

        } else {

            return $linkList;

        }

    }

    public static function addJsAssets()
    {
        if (class_exists('jquery')) {

            jquery::addJsAssets();

        }
                
        Event::$data['js'][] = self::renderLinks();
    }

    public static function minify($content) 
    {
        if (empty($content)) return $content;

        return $content;

        // driver name
        $driver = 'Minify_Js_Driver';

        // load stuff (stolen from Minify who stole it from Kohana core libs)
        if ( ! Kohana::auto_load($driver)) {

            kohana::log('error', 'Unable to load the minify driver ' . $driver);

            return $content;

        }

        // load the driver
        $minify = new $driver($content);

        // minify the page
        return $minify->min();
    }
}