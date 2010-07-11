<?php
/*
	
	== PHP FILE TREE ==
	
		Let's call it...oh, say...version 1?
	
	== AUTHOR ==
	
		Cory S.N. LaViska
		http://abeautifulsite.net/
		
	== DOCUMENTATION ==
	
		For documentation and updates, visit http://abeautifulsite.net/notebook.php?article=21
		
*/

class filetree {
    public static function php_file_tree($directory, $return_link, $extensions = array(), $excludeRegEx = NULL) {
            // Generates a valid XHTML list of all directories, sub-directories, and files in $directory
            // Remove trailing slash
            if( substr($directory, -1) == "/" ) $directory = substr($directory, 0, strlen($directory) - 1);
            $code = self::php_file_tree_dir($directory, $return_link, $extensions, $excludeRegEx, '/');
            return $code;
    }

    public static function php_file_tree_dir($directory, $return_link, $extensions = array(), $excludeRegEx = NULL, $first_call = true, $depthDir = '') {
            // Recursive function called by php_file_tree() to list directories/files

            // Get and sort directories/files
            $file = scandir($directory);
            natcasesort($file);
            // Make directories first
            $files = $dirs = array();
            foreach($file as $this_file) {
                    if( is_dir("$directory/$this_file" ) )
                        $dirs[] = $this_file;
                    else
                        $files[] = $this_file;
            }
            if ($extensions === FALSE) {
                // If FALSE has been specified explicitly, assume no file listing
                $file = $dirs;
            } else {
                // Otherwise, merge the list of files and directories together
                $file = array_merge($dirs, $files);
            }

            // Filter unwanted extensions
            if( !empty($extensions) or (!empty($excludeRegEx)) ) {
                    foreach( array_keys($file) as $key ) {
                            if( !is_dir("$directory/$file[$key]") ) {
                                if (!empty($extensions)) {
                                    $ext = substr($file[$key], strrpos($file[$key], ".") + 1);
                                    if( !in_array($ext, $extensions) ) unset($file[$key]);
                                }
                            }

                            // Regexs work on directories, too, so they are outside the is_dir() block (above)
                            if (!empty($excludeRegEx)) {
                                if (preg_match($excludeRegEx, $file[$key]) > 0) {
                                    unset ($file[$key]);
                                }
                            }
                    }
            }

            $php_file_tree = '';
            if( count($file) > 2 ) { // Use 2 instead of 0 to account for . and .. "directories"
                    $php_file_tree = "<ul";
                    if( $first_call ) { $php_file_tree .= " class=\"php-file-tree\""; $first_call = false; }
                    $php_file_tree .= ">";
                    foreach( $file as $this_file ) {
                            if( $this_file != "." && $this_file != ".." ) {
                                    if( is_dir("$directory/$this_file") ) {
                                            // Directory
                                            $link = '#'; // By default, directories do not have links
                                            // If we are scanning only for directories, we optionally link the last level directory to a JS/link string

                                            //echo $this_file . ' = ' . $recursiveResult . '<BR>';
                                            $php_file_tree .= "<li class=\"pft-directory\">";

                                            // Scan for any subfolders/trees (recursive)
                                            $subTree = self::php_file_tree_dir("$directory/$this_file", $return_link ,$extensions, $excludeRegEx, false, $depthDir . $this_file . '/');
                                            if (!$subTree) {
                                                $link = $return_link;
                                                $link = str_replace("[link]", $depthDir . urlencode($this_file) . '/', $return_link);
                                            }

                                            // Add current element
                                            $php_file_tree .= "<a href=\"#\" onClick=\"$link\">" . htmlspecialchars($this_file) . "</a>";
                                            // Add sublevels/trees
                                            $php_file_tree .= $subTree;

                                            $php_file_tree .= "</li>";
                                    } else {
                                            // File
                                            // Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
                                            $ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);
                                            $link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
                                            $php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\">" . htmlspecialchars($this_file) . "</a></li>";
                                    }
                            }
                    }
                    $php_file_tree .= "</ul>";
            }
            return $php_file_tree;
    }


}
