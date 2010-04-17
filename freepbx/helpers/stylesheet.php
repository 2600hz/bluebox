<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * stylesheet helper
 *
 * @author Karl Anderson
 * @license LGPL
 * @access public
 */
class stylesheet {
    public static $cache = FALSE;
    
    protected static $csstags = array(
        'codeblock' => "<style type=\"text/css\">\n%s</style>\n",
    );

    protected static $cssconditional = array (
        'ie' => '<!--[if IE]>%s<![endif]-->',
        'except_ie' => '<!--[if !IE]>%s<![endif]-->',
        'ie7' => '<!--[if IE 7]>%s<![endif]-->',
        'ie6' => '<!--[if IE 6]>%s<![endif]-->',
        'ie5' => '<!--[if IE 5]>%s<![endif]-->',
        'ie6_or_lower' => '<!--[if lte IE 6]>%s<![endif]-->',
        'ie7_or_lower' => '<!--[if lte IE 7]>%s<![endif]-->',
        'ie8_or_lower' => '<!--[if lte IE 8]>%s<![endif]-->',
        'ie6_or_above' => '<!--[if gte IE 6]>%s<![endif]-->',
        'ie7_or_above' => '<!--[if gte IE 7]>%s<![endif]-->',
        'ie8_or_above' => '<!--[if gte IE 8]>%s<![endif]-->'
    );
    
    protected static $blockOptions = array();

    protected static $outputBuffer = NULL;

    protected static $stylePaths = array();

    protected static $styleBlocks = array();

    public static function codeBlock($style = NULL, $options = array()) {
        // if options is not an array, the consider it a weight
        if (!is_array($options)) {
            $options = array('weight' => (int)$options);
        }

        // ensure our defualt options are populated
        $options += array(
            'minify' => FALSE,
            'inline' => FALSE,
            'weight' => 40,
            'cond' => NULL
        );
        extract($options);

        // standardize the condition name as a lower case string
        $cond = strtolower($cond);

        // if style is empty then they are starting a new code block
        if (empty($style)) {
            // mark that the style is in the output buffer
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

        // determine how we are outputting the file
        if ($inline) {
            // if we want to minify this and the minify driver is avaliable
            if ($minify) {
                $style = self::minify($style);
            }
            // check if this is a conditional css statement
            if (array_key_exists($cond, self::$cssconditional)) {
                // if so wrap it in a conditional tag
                $style = sprintf(self::$cssconditional[$cond], $style);
            }
            // create the code block
            return sprintf(self::$csstags['codeblock'], $style);
        } else {
            // if we are using cache to store the code blocks....
            if (self::$cache) {
                $cache = Cache::instance();
                $cacheName = 'css_'.sha1($style);

                // see if this script is already cached, if not do so
                $cached = $cache->get($cacheName);
                if (is_null($cached)) {
                    $cache->set($cacheName, $style, array(), Kohana::config('cache.default.lifetime'));
                }

                // only store the cache id
                $style = $cacheName;
            }
            // if this is a conditional code block store it seperate from
            // standard block
            if (array_key_exists($cond, self::$cssconditional)) {
                self::$styleBlocks[$cond][$weight][] = $style;
            } else {
                self::$styleBlocks['codeblocks'][$weight][] = $style;
            }
        }
    }

    public static function blockEnd() {
        // see if we are being called with an open style output buffer
        if (empty(self::$blockOptions['inBlock'])) {
            return;
        }
        
        // get the scrip out of the buffer
        $style = @ob_get_contents();

        // clean and restart the buffer
        @ob_end_clean();
        ob_start();
        // restore the buffer to its previous state
        echo self::$outputBuffer;

        // null our temporary storage
        self::$outputBuffer = NULL;

        // get the options
        $options = self::$blockOptions;
        self::$blockOptions = array();
        
        // if we didnt recieve anything then move on
        if (empty($style)) {
            return;
        }
        // process what we got in the style buffer
        echo self::codeBlock($style, $options);
    }

    public static function add($paths, $options = array(), $directory = 'assets/css') {
        // if they didnt give us anything to include move on
        if (empty($paths)) return FALSE;

        // if options is not an array assume it is a weight
        if (!is_array($options)) {
            $options = array('weight' => (int)$options);
        }

        // ensure our defualt options are populated
        $options += array(
            'minify' => FALSE,
            'inline' => FALSE,
            'asCodeBlock' => FALSE,
            'scan' => TRUE,
            'docroot' => DOCROOT,
            'weight' => 40,
            'cond' => NULL
        );
        extract($options);

        // standardize the condition name as a lower case string
        $cond = strtolower($cond);

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
                    $extn = 'css';
                } else {
                    $path = substr($path, 0, -1 * (strlen($extn) + 1));
                }

                // first check the modules for the assets
                $search = $directory .$path .'.' .$extn;
                if($found = Kohana::find_file(rtrim($directory, '/'), $path, FALSE, $extn)) {
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
                    kohana::log('error', 'Unable to locate CSS include ' .$path);
                    continue;
                }
            }

            // are we putting this file into a code block or inline?
            if ($inline || $asCodeBlock) {
                // get the file contents
                $style = @file_get_contents($path);
                if (empty($style)) {
                    kohana::log('error', 'Unable to read css file '. $path);
                    continue;
                }
                // make the file contents into a code block
                echo self::codeBlock($style, $options);
            } else {
                // if this is a conditional code block store it seperate from
                // standard block
                if (array_key_exists($cond, self::$cssconditional)) {
                    self::$stylePaths[$cond][$weight][] = $path;
                } else {
                    self::$stylePaths['paths'][$weight][] = $path;
                }
            }
        }
        // is_file caches info about the files we looked for, clear that now
        clearstatcache();
        return TRUE;
    }

    public static function renderCodeBlocks($inline = TRUE, $options = array()) {
        // if options is not an array assume it is the minify option
        if (!is_array($options)) {
            $options = array('minify' => (bool)$options);
        }

        // ensure our defualt options are populated
        $options += array(
            'minify' => FALSE
        );
        extract($options);

        // build a var with the css code blocks
        $styleblocks = self::$styleBlocks;
        self::$styleBlocks = array();

        $cache = Cache::instance();

        // loop all the css includes and determine what needs to go on this page
        // and in what order
        $blockOutputBuffer = array();
        foreach ($styleblocks as $cond => $condblock) {
            // sort this condblock of codeblocks by weight
            ksort($condblock);
            $blockOutputBuffer[$cond] = array();
            foreach ($condblock as $codeblocks) {
                // if we are storing these codeblocks in cache then get them now
                if (self::$cache) {
                    // since they are in cache we have to do each one
                    foreach ($codeblocks as $cacheName) {
                        // check if we are minify'n these style blocks
                        if ($minify) {
                            // see if we have already minified this
                            $cacheMinName = 'css_min_' .substr($cacheName, 4);
                            $cached = $cache->get($cacheMinName);
                            // if this style is not already cached minified do so
                            if (is_null($cached)) {
                                // try to get the un-minified style
                                $cached = $cache->get($cacheName);
                                if (is_null($cached)) {
                                    kohana::log('error', 'Unable to minify cached codeblock  ' .$style);
                                    continue;
                                }
                                // minify and cache this style
                                $cached = self::minify($cached);
                                $cache->set($cacheMinName, $cached, array(), Kohana::config('cache.default.lifetime'));
                            }
                            // add the minified style to the output buffer
                            $blockOutputBuffer[$cond][] =  $cached;
                        } else {
                            // see if we can get this style out of cache
                            $cached = $cache->get($cacheName);
                            if (is_null($cached)) {
                                kohana::log('error', 'Unable to locate cached codeblock ' .$style);
                                continue;
                            }
                            // add the style to the output buffer
                            $blockOutputBuffer[$cond][] =  $cached;
                        }
                    }
                } else {
                    // if we are not storing the style in cache then the array
                    // contains the code blocks directly
                    $blockOutputBuffer[$cond] = array_merge($blockOutputBuffer[$cond], $codeblocks);
                }
            }
        }

        // run through everything that needs to be on the page and
        // make in conditional where apropriate then convert them into css links
        $codeblock = '';
        foreach ($blockOutputBuffer as $cond => $codeblocks) {
            // if all the includes are already on page move on
            if (empty($codeblocks)) continue;
            // combine these blocks into a string
            $block = sprintf(self::$csstags['codeblock'], implode("\n", $codeblocks));
            // if these are conditional css blocks then wrap them
            // in the conditional statement
            if (array_key_exists($cond, self::$cssconditional)) {
                $block = sprintf(self::$cssconditional[$cond], $block);
            }
            // add these blocks to our string
            $codeblock .= $block;
        }

        // are we displaying this inline or returning the string
        if ($inline) {
            echo $codeblock;
        } else {
            return $codeblock;
        }
    }

    public static function renderLinks($inline = FALSE, $index = FALSE) {
        // build a var with the included css files
        $styles = self::$stylePaths;
        self::$stylePaths = array();

        // Get the session so we can track what assets are on the page
        $session = Session::instance();
        // If this is a full page load (ie not ajax) then there are no assest already on page
        if (!request::is_ajax()) $session->delete('stylesheet.onPageAssets');
        // Initialize the vars to track what has been added, and convience wrappers
        $onPageAssets = $session->get('stylesheet.onPageAssets', array());

        // loop all the css includes and determine what needs to go on this page
        // and in what order
        $styleOutputBuffer = array();
        foreach ($styles as $cond => $condblock) {
            // sort this condblock of style paths by weight
            ksort($condblock);
            $styleOutputBuffer[$cond] = array();
            foreach ($condblock as $stylePaths) {
                // make sure there are no duplicates in this array
                $stylePaths = array_unique($stylePaths);
                // make sure these css links are not already on the page
                // via ajax/parent or in another weight category (lowest weight wins)
                $stylePaths = array_diff($stylePaths, $onPageAssets);
                // add the filtered list to the array of assets to put on the page
                $styleOutputBuffer[$cond] = array_merge($styleOutputBuffer[$cond], $stylePaths);
                // keep track of what we add to the pages
                $onPageAssets = array_merge($onPageAssets, $stylePaths);
            }
        }

        // Track what assest we added to this page
        $session->set('stylesheet.onPageAssets', $onPageAssets);

        // run through everything that needs to be on the page and
        // make in conditional where apropriate then convert them into css links
        $linkList = '';
        foreach ($styleOutputBuffer as $cond => $stylePaths) {
            // if all the includes are already on page move on
            if (empty($stylePaths)) continue;
            // create the css links
            $links = html::stylesheet($stylePaths, 'screen', $index);
            // if these are conditional css pages then wrap the links
            // in the conditional statement
            if (array_key_exists($cond, self::$cssconditional)) {
                $links = sprintf(self::$cssconditional[$cond], "\n".rtrim($links, "\n")."\n");
            }
            // add these links to our string
            $linkList .= $links;
        }

        // are we displaying this inline or returning the string
        if ($inline) {
            echo $linkList;
        } else {
            return $linkList;
        }
    }

    public static function addCssAssets() {
        // if there is a jquery class avalible then get it to add any
        // css includes it needs at this time
        if (class_exists('jquery')) {
            jquery::addCssAssets();
        }

        // otherwise add our links to the events css collection string
        Event::$data['css'][] = self::renderLinks();
        // render any css code blocks in the header as well
        Event::$data['css'][] = self::renderCodeBlocks(FALSE);
    }

    public static function minify($content) {
        if (empty($content)) return $content;
        
        // driver name
        $driver = 'Minify_Css_Driver';

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
?>