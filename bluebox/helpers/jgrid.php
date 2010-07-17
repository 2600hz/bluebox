<?php defined('SYSPATH') or die('No direct access allowed.');
define('DELIM', '/');
/**
 * @package    Core/Helpers/Jgrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class jgrid
{
    /**
     * @var string This is a grid name used to identify the post and support multiple grids
     */
    protected $gridName = '';
    /**
     * @var $query array Holds a multidimensional array representing a doctrine query
     */
    protected $query = array();
    /**
     * @var $jquery array Holds the JS parameters for a jqgrid
     */
    protected $jquery = array();
    /**
     * @var $baseModel string The name of the base model
     */
    protected $baseModel = '';
    /**
     * @var $baseAlias string A convience var for the base model alias
     */
    protected $baseAlias = '';
    /**
     * @var $doctrineMethods array An array of doctrine methods to search for in the query array (except from)
     */
    protected $doctrineMethods = array(
        'select',
        'leftJoin',
        'where',
        'addWhere',
        'andWhere',
        'orWhere',
        'whereIn',
        'andWhereIn',
        'orWhereIn',
        'whereNotIn',
        'andWhereNotIn',
        'orWhereNotIn'
    );
    /**
     * The constructor initializes the various parameters of jgrid and
     * loads default JS parameters for the grid as well as any user options
     *
     * @return void
     * @param string $baseModel The baseModel name
     * @param array $options[optional] An array of parameters for the grid in key value pairs
     */
    public function __construct($baseModel, $options = array())
    {
        // Initialize the baseModel and baseAlias parameters
        $this->baseModel = $baseModel;
        $this->baseAlias = self::_getAlias($baseModel);
        // Add the from doctrine query method for this query
        $this->query['from'][$this->baseAlias] = $baseModel . ' ' . $this->baseAlias;
        // If the gridName is not explictly set then make attempt a unqiue (repeatable) name
        if (!empty($options['gridName'])) {
            $this->gridName = $options['gridName'];
            unset($options['gridName']);
        } else {
            $hash = 'XXX';
            foreach($options as $option) {
                if (is_string($option)) $hash.= $option;
            }
            $this->gridName = strtolower($baseModel) . '_' . substr(md5($hash) , 10, 6);
        }
        // Set some initial defaults for the jqgrid parameters
        $this->jquery['url'] = $_SERVER['REQUEST_URI'] . '?q=2';
        $this->jquery['datatype'] = 'json';
        $this->jquery['imgpath'] = url::base() . 'assets/css/jqGrid/steel/images';
        $this->jquery['viewrecords'] = true;
        $this->jquery['caption'] = $baseModel;
        $this->jquery['rowList'] = array(
            '12',
            '25',
            '50',
            '100',
            '250'
        );
        $this->jquery['rowNum'] = '12';
        $this->jquery['height'] = 'auto';
        $this->jquery['autowidth'] = true;
        $this->jquery['recordtext'] = __('View') . ' {0} - {1} ' . __('of') . ' {2}';
        $this->jquery['emptyrecords'] = __('No records to view');
        $this->jquery['loadtext'] = __('Loading') . '...';
        $this->jquery['pgtext'] = __('Page') . ' {0} ' . __('of') . ' {1}';
        // Merge the user options with the defaults
        $this->jquery = arr::merge($this->jquery, $options);
        $this->jquery['caption'] = self::_i18n($this->jquery['caption']);
        // I dont want the user to change this
        $this->jquery['postData']['gridName'] = $this->gridName;
    }
    /**
     * This is a factory for constructing a new grid instance
     *
     * @return jgrid
     * @param string $baseModel The baseModel name
     * @param array $options[optional] An array of parameters for the grid in key value pairs
     */
    public function grid($baseModel, $options = array())
    {
        return new jgrid($baseModel, $options);
    }
    /**
     * This function renders a restful grid and executes any restful requests for
     * that grid
     *
     * @param array $options (placeholder, who knows)
     * @return mixed
     */
    public function produce($options = array())
    {
        /** TODO: this needs to make sure any reply is for this grid!!!!
         * if (empty($_REQUEST['gridName']) || $_REQUEST['gridName'] != $this->gridName) return false;
         */
        $options += array (
            'doctrine_query' => NULL,
            'createNavGrid' => true,
            'htmlAttributes' => array()
        );
        $oper = kohana::$instance->input->post('oper', 'none');
        kohana::log('debug', 'Grid RESTful operator \'' .$oper .'\'');
        switch ($oper) {
            case 'add':
                kohana::$instance->auto_render = FALSE;
                if (empty($this->jquery['navGrid']['options']['add'])) {
                    message::set('Attempted to add row on grid without that capability!');
                } else {
                    $this->restfulAdd();
                }
                $this->_renderRESTfulErrors();
                break;
            case 'edit':
                kohana::$instance->auto_render = FALSE;
                if (empty($this->jquery['navGrid']['options']['edit'])) {
                    message::set('Attempted to edit row on grid without that capability!');
                } else {
                    $this->restfulEdit();
                }
                $this->_renderRESTfulErrors();
                break;
            case 'del':
                if (empty($this->jquery['navGrid']['options']['del'])) {
                    message::set('Attempted to delete row on grid without that capability!');
                } else {
                    $this->restfulDelete();
                }
                $this->_renderRESTfulErrors();
                break;
            case 'getMessages':
                kohana::log('debug', 'Rendering any messages for the grid');
                $this->_renderRESTfulErrors();
                break;
            default:
                if (!empty($_REQUEST['gridName'])) {
                    kohana::log('debug', 'Unknown or missing operator with gridName, assuming request for JSON');
                    if ($json = $this->getGridJson($options['doctrine_query'])) {
                        kohana::$instance->auto_render = FALSE;
                        echo $json;
                    } else {
                        kohana::log('debug', 'Request for JSON does not belong to grid ' .$this->gridName);
                    }
                } else {
                    kohana::log('debug', 'Unknown or missing operator without gridName, assuming request to render grid');
                    return $this->render($options['createNavGrid'], $options['htmlAttributes']);
                }
        }
    }
    /**
     * This function preforms the default restful add,
     * extend this class and redefine if you need different behavoir.
     *
     * @return void
     */
    public function restfulAdd()
    {
        kohana::log('debug', 'RESTful add has no default method yet!');
    }
    /**
     * This function preforms the default restful edit,
     * extend this class and redefine if you need different behavoir.
     *
     * @return void
     */
    public function restfulEdit()
    {
        kohana::log('debug', 'RESTful edit has no default method yet!');
    }
    /**
     * This function preforms the default restful delete,
     * extend this class and redefine if you need different behavoir.
     *
     * @return void
     */
    public function restfulDelete()
    {
        $errorOccured = FALSE;
        kohana::log('debug', 'Attempting a RESTful delete');
        if (empty($_POST['id'])) {
            message::set('No rows where specified for delete', array('type' => 'alert'));
            return;
        }
        $delIDs = explode(',', $_POST['id']);
        $conn = Doctrine_Manager::connection();
        foreach($delIDs as $delID) {
            $row = Doctrine::getTable($this->baseModel)->find($delID);
            if (!$row) {
                $errorOccured = TRUE;
                message::set('Unable to locate row ' . strtolower($this->baseModel) . ' id '. $delID . '!');
                continue;
            }
            try {
                Bluebox_Record::setBaseSaveObject($row);
                $conn->beginTransaction();
                plugins::delete($row);
                $row->delete();
                $conn->commit();
                plugins::delete($row, array(
                    'custom' => Router::$controller . '.success',
                    'coreAction' => FALSE,
                    'core' => FALSE
                ));
                Bluebox_Record::setBaseSaveObject(NULL);
            } catch(Exception $e) {
                $errorOccured = TRUE;
                message::set('Unable to delete ' . strtolower($this->baseModel) . ' id '. $delID . '! '  . $e->getMessage());
            }
        }
        if (empty($errorOccured)) {
            message::set('Selected record(s) deleted.', array('type' => 'success'));
        }
    }
     /**
     * This adds a column to the grid.  If the path does not contain a DELIM it is considered in relation
     * to the baseModel otherwise a new model is loaded.
     *
     * @return jgrid
     * @param string $path The path to a field with optional Model to retriev it from
     * @param string $displayName The display name of the coulmn
     * @param array $options[optional] A key value pair array of options for this column
     * @param bool $mergeDuplicate[optional] If a column has already been added this chooses to ingnore or merge the new addition
     * @param bool $addToGrid[optional] This chooses to add a column to a grid (hence add to a query but not a grid)
     */
    public function add($path, $displayName, $options = array() , $mergeDuplicate = true, $addToGrid = true)
    {
        // Split the path up into its parts and set the parts as var names in this method
        extract(self::_splitPath($path));
        /**
         *
         * THIS SECTION TRACKS WHICH RESULT COLUMNS WILL BE TURNED INTO LINKS
         *
         */
        if (!empty($options['link'])) {
            // Save the links
            $this->query['links'][$hydrateName] = $options['link'];
            // Build an array of columns that the links need so we can ensure they are inclued in the query
            self::_additionalArg($options['link']['arguments'], 'link');
            // This shouldnt be passed to jqgrid options
            unset($options['link']);
        }
        /**
         *
         * THIS SECTION TRACKS WHICH RESULT COLUMNS HAVE CALLBACKS
         *
         */
        if (!empty($options['callback'])) {
            // Save the callback
            $this->query['callbacks'][$hydrateName] = $options['callback'];
            // Build an array of columns that the callbacks need so we can ensure they are inclued in the query
            self::_additionalArg($options['callback']['arguments'], 'callback');
            // This shouldnt be passed to jqgrid options
            unset($options['callback']);
        }
        /**
         *
         * THIS SECTION ADDS COLUMNS TO JQGRID
         *
         */
        if ($addToGrid) {
            if (!empty($options['columnWeight'])) {
                $columnWeight = (int)$options['columnWeight'];
                unset($options['columnWeight']);
            } else {
                $columnWeight = 0;
            }
            $this->query['columns'][$columnWeight][$hydrateName] = self::_i18n($displayName);
            // Ensure the colModel is in the same place
            $colModel = & $this->jquery['colModel'][$columnWeight][$hydrateName];
            // Add default colModel parameters
            $colModel = array(
                'name' => $hydrateName,
                'index' => $dqlColumn
            );
            // Merge the default and column options
            $colModel = arr::merge($options, $colModel);
        }
        /**
         *
         * THIS SECTION HANDLES ADDING FIELDS AND TABLES TO THE DOCTRINE QUERY
         *
         */
        try {
            // Test if both the table and column exist, if not skip adding this to the query (grid only)

            if (class_exists($model)) {
                $table = Doctrine::getTable($model);
            } else {
                kohana::log('alert', 'Unable to locate model ' . $model . ' checking for alias on baseModel');
                $baseTable = Doctrine::getTable($this->baseModel);

                $relations = $baseTable->getRelations();

                if (array_key_exists($model, $relations)) {
                    kohana::log('alert', 'Found alias ' . $model . ' is for a relationship to model ' .  $relations[$model]['class']);
                    $table = Doctrine::getTable($relations[$model]['class']);
                } else {
                    throw new Exception('Unable to locate ' . $model);
                }
            }

            if (!array_key_exists($column, $table->getColumns())) {
                throw new Exception('Column ' . $column . ' doesnt exist in ' . $model);
            }
            // If the column doesnt exist already or we are allowed to update existing...
            if (empty($this->query['select'][$column]) || $mergeDuplicate) {
                // Add a left join to the doctrine query, unless it is the base model
                if ($model != $this->baseModel) $this->query['leftJoin'][$modelAlias] = $this->baseAlias . '.' . $model . ' ' . $modelAlias;
                // ...add a select statement to the doctrine query for this path
                $this->query['select'][$column] = $dqlColumn;
            }
        }
        catch(Exception $e) {
            kohana::log('alert', 'Adding field to DQL failed (treating as non db source): ' . $e->getMessage());
        }
        // Return the jgrid instance for chaining
        return $this;
    }
    /**
     * This function lets you edit the default buttons on the navGrid div
     * For a list of parameters see http://www.secondpersonplural.ca/jqgriddocs/_2er0j2mvk.htm
     *
     * @return jgrid
     * @param object $options[optional]
     */
    public function navGrid()
    {
        $args = func_get_args();

        foreach ($args as $argCount => $arg) {
            if (empty($arg) || !is_array($arg)) continue;
            switch ($argCount) {
                case 0:
                    // options
                    // if we are activating a restful action then write a URL if there is none
                    if (!empty($arg['edit']) && empty($this->jquery['navGrid']['edit']['url'])) {
                        $this->jquery['navGrid']['edit']['url'] = $_SERVER['REQUEST_URI'] . '?gridName=' . $this->gridName;
                    }
                    // if we are activating a restful action then write a URL if there is none
                    if (!empty($arg['add']) && empty($this->jquery['navGrid']['add']['url'])) {
                        $this->jquery['navGrid']['add']['url'] = $_SERVER['REQUEST_URI'] . '?gridName=' . $this->gridName;
                    }
                    // if we are activating a restful action then write a URL if there is none
                    if (!empty($arg['del']) && empty($this->jquery['navGrid']['del']['url'])) {
                        $this->jquery['navGrid']['del']['url'] = $_SERVER['REQUEST_URI'] . '?gridName=' . $this->gridName;
                        $this->jquery['navGrid']['del']['afterSubmit'] = 'function (resMsg) {  $(\'#' .$this->gridName .'AjaxMessageReceiver\').html(resMsg.responseText); return [true]; }';
                    }    
                    $navGrid = &$this->jquery['navGrid']['options'];
                    break;
                case 1:
                    // edit
                    $navGrid = &$this->jquery['navGrid']['edit'];
                    break;
                case 2:
                    // add
                    $navGrid = &$this->jquery['navGrid']['add'];
                    break;
                case 3:
                    // delete
                    $navGrid = &$this->jquery['navGrid']['del'];
                    break;
                case 4:
                    // search
                    $navGrid = &$this->jquery['navGrid']['search'];
                    break;
                case 5:
                    // search
                    $navGrid = &$this->jquery['navGrid']['view'];
                    break;
                default:
                    continue 2;
                    break;
            }
            // if there are already options in the grid then merge these in
            if (is_array($navGrid)) {
                $navGrid = arr::merge($navGrid, $arg);
            } else {
                // new
                $navGrid = $arg;
            }
        }
        return $this;
    }
    /**
     * This function lets you add a custom button to the navGrid div
     * For a list of parameters see http://www.secondpersonplural.ca/jqgriddocs/_2ev0lsi7v.htm
     *
     * @return jgrid
     * @param object $options[optional]
     */
    public function navButtonAdd($caption, $options = array())
    {
        if (!empty($options['noCaption'])) {
            $options['caption'] = '';
            unset($options['noCaption']);
        } else {
            $options['caption'] = self::_i18n($caption);
        }
        if (!empty($options['title'])) $options['title'] = self::_i18n($options['title']);
        $this->jquery['navButton'][$caption] = $options;
        return $this;
    }
    /*
    * This function lets you set the options for the search Grid, for a list
    * of parameters see http://www.secondpersonplural.ca/jqgriddocs/_2eb0goupg.htm
    *
    * TODO: Put code in me!
    *
    * @return jgrid
    * @param array $options[optional] An array of parameters for the jqgrid.searchGrid
    */
    public function searchGrid($options = array())
    {
        return $this;
    }
    /**
     * This function implements the doctrine where clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function where($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'where');
        return $this;
    }
    /**
     * This function implements the doctrine addWhere clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function andWhere($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'andWhere');
        return $this;
    }
    /**
     * This function implements the doctrine orWhere clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function orWhere($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'orWhere');
        return $this;
    }
    /**
     * This function implements the doctrine whereIn clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function whereIn($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'whereIn');
        return $this;
    }
    /**
     * This function implements the doctrine andWhereIn clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function andWhereIn($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'andWhereIn');
        return $this;
    }
    /**
     * This function implements the doctrine orWhereIn clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function orWhereIn($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'orWhereIn');
        return $this;
    }
    /**
     * This function implements the doctrine whereNotIn clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function whereNotIn($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'whereNotIn');
        return $this;
    }
    /**
     * This function implements the doctrine orWhereNotIn clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     */
    public function orWhereNotIn($path, $operator, $parameter = '~NULL~')
    {
        self::_where($path, $operator, $parameter, 'orWhereNotIn');
        return $this;
    }
    public function addAction($url, $name, $options = array())
    {
        // Move the url to a key 'link' in the options array becuase we are using
        // a private function that expects it there internally
        $options['link'] = $url;
        // Split up the URL and column options
        $validUrlOptions = array_flip(array(
            'link',
            'arguments',
            'reqAllArgs',
            'attributes',
            'passAsSegments'
        ));
        $columnOptions = array_diff_key($options, $validUrlOptions);
        $urlOptions = array_intersect_key($options, $validUrlOptions);
        // If this column has no options yet load it with an empty array
        if (empty($this->query['actionsColumn'])) $this->query['actionsColumn'] = array();
        // If the option to change the width has been added handle it separately
        if (!empty($columnOptions['width'])) {
            // Possibly get a pointer to the current columns width
            $columnWidth = & $this->query['actionsColumn']['width'];
            // If the width is not set or the new width is greater then update the columns options
            // As actions are added they can only increase the column width...
            if (empty($columnWidth) || $columnWidth < $columnOptions['width']) $columnWidth = $columnOptions['width'];
            // Dont let this option merge
            unset($columnOptions['width']);
        }
        // Merge all the incoming column options
        $this->query['actionsColumn'] = arr::merge($columnOptions, $this->query['actionsColumn']);
        // Add this action to the column
        $this->query['actions'][$name] = $urlOptions;
        // Build an array of columns that the links need so we can ensure they are inclued in the query
        self::_additionalArg($urlOptions['arguments'], 'actions');
        return $this;
    }
    /**
     * This function buils the HTML for a grid, you can choose to render the paging/nav bar
     * and supply any additional HTML attributes for either the table of the nav div in a array
     * such that the array looks like:
     *
     * array(
     * 	'table' => array ('id' => 'someID', 'class'=> 'gridTable', 'extra' => 'onclick="alert('test');"),
     * 	'div' => array ('style' => 'text-align: center;')
     * )
     *
     * @return string grid HTML
     * @param bool $createNavGrid[optional]
     * @param array $attributes[optional]
     */
    public function render($createNavGrid = true, $attributes = array())
    {
        /**
         *
         * THIS SECTION GENERATES HTML FOR A TABLE
         *
         */
        $html = '<div id="' .$this->gridName .'AjaxMessageReceiver" style="display:none;">&nbsp;</div>';
        // If the user has not supplied an id then we will gen one for the table
        if (empty($attributes['table']['id'])) $attributes['table']['id'] = $this->gridName;
        // We need to add a default class to the table
        $attributes['table']['class'] = empty($attributes['table']['class']) ? 'scroll jqgrid_instance' : $attributes['table']['class'] . ' scroll jqgrid_instance';
        // This gets any extra attributes and unsets it so the form helper will not parse it
        if (!empty($attributes['table']['extra'])) {
            $extra = $attributes['table']['extra'];
            unset($attributes['table']['extra']);
        } else {
            $extra = '';
        }
        // Build the HTML for the table
        $html .= '<table' . form::attributes($attributes['table']) . ' ' . $extra . '><tr><td></td></tr></table>' . "\n";
        /**
         *
         * THIS SECTION GENERATES HTML FOR A NAVIGATION DIV
         *
         */
        $customNavButtons = array();
        $navGrid = array(
            'options' => array(),
            'edit' => array(),
            'add' => array(),
            'del' => array(),
            'search' => array(),
            'view' => array()
        );
        if ($createNavGrid) {
            // If the user has not supplied an id then we will gen one for the div
            if (empty($attributes['div']['id'])) $attributes['div']['id'] = 'pager_' . $attributes['table']['id'];
            // We need to add a default class to the div
            $attributes['div']['class'] = empty($attributes['div']['class']) ? 'scroll' : $attributes['div']['class'] . ' scroll';
            // This gets any extra attributes and unsets it so the form helper will not parse it
            if (!empty($attributes['div']['extra'])) {
                $extra = $attributes['div']['extra'];
                unset($attributes['div']['extra']);
            } else {
                $extra = '';
            }
            // Build the HTML for the div
            $html.= '<div' . form::attributes($attributes['div']) . ' ' . $extra . '></div>' . "\n";
            // Add the pager div ID to the grid parameters
            $this->jquery['pager'] = '#' . $attributes['div']['id'];

            // build the array of navGrid options, setting defaults as we go
            if (isset($this->jquery['navGrid'])) {
                $this->jquery['navGrid'] += $navGrid;
                $navGrid = $this->jquery['navGrid'];
                unset($this->jquery['navGrid']);
            }
            $navOptions = array(
                'edit' => false,
                'add' => false,
                'del' => false,
                'search' => true,
                'view' => false
            );
            $navGrid['options'] = arr::merge($navOptions, $navGrid['options']);

            // build an array of navButtons
            if (!empty($this->jquery['navButton'])) {
                $customNavButtons = $this->jquery['navButton'];
                unset($this->jquery['navButton']);
            }
        }
        /**
         *
         * THIS SECTION GENERATES JS
         *
         */
        self::_orderColumns();
        $this->jquery['colNames'] = array_values($this->query['columns']);
        if (!empty($this->query['actions'])) {
            // Add this column name to the jqgrid colName headers
            $this->jquery['colNames'][] = '<div style="text-align:center;">' .__('Actions') .'</div>';
            // A convience wraper for adding a colModel to jqgrid
            $colModel = & $this->jquery['colModel'][];
            // Accept any custom parameters for the action column
            if (!empty($this->query['actionsColumn'])) $colModel = $this->query['actionsColumn'];
            // Add a set of non-overridable defaults and save it as the column model
            $colModel['name'] = 'actions';
            $colModel['search'] = false;
            $colModel['sortable'] = false;
            $colModel['align'] = 'center';
        }
        jquery::addPlugin('betagrid');  
        $jqueryGrid = jquery::addQuery('#' . $attributes['table']['id'])->jqGrid($this->jquery);
        // This has to come after the jquery helper but the above if block for $createNavGrid must come before
        if ($createNavGrid) {

            $jqueryNavGrid = $jqueryGrid->navGrid('#' . $attributes['div']['id'],
                $navGrid['options'],
                $navGrid['edit'],
                $navGrid['add'],
                $navGrid['del'],
                $navGrid['search']
                //$navGrid['view'] //This causes issue, so the docs are out of date or something
            );
            if (!empty($customNavButtons)) {
                foreach($customNavButtons as $customNavButton) {
                    // Replace the keywords with values
                    $clickFunc = & $customNavButton['onClickButton'];
                    if (!empty($clickFunc)) {
                        $clickFunc = str_replace(array(
                            '{table_id}',
                            '{pager_id}',
                        ) , array(
                            $attributes['table']['id'],
                            $attributes['div']['id'],
                        ) , $clickFunc);
                    }
                    // Build the JS for a new navButton
                    $jqueryNavGrid->navButtonAdd('#' . $attributes['div']['id'], $customNavButton);
                }
            }
        }
        // Return the html string
        return $html;
    }
    /**
     * This performs the actual doctrine query and returns a json object
     *
     * @return json object
     */
    public function getGridJson($q = NULL)
    {
        // If this is not a request for this grids data, exit
        if (empty($_REQUEST['gridName']) || $_REQUEST['gridName'] != $this->gridName) return false;
        /**
         *
         * THIS SECTION INITIALIZES ANY VARS EXPECTED FROM THE GRID
         *
         */
        self::_orderColumns();
        // These are the standard sort and page requests from the grid
        $currentPage = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];
        $resultsPerPage = empty($_REQUEST['rows']) ? 10 : $_REQUEST['rows'];
        $sidx = & $_REQUEST['sidx'];
        $sord = & $_REQUEST['sord'];
        // If the user has not supplied a Doctrine query then create one
        if (empty($q)) $q = self::_autoQuery();
        /**
         *
         * THIS SECTION GETS A DOCTRINE QUERY, ADDS A ORDERBY ARGUMENT AND WRAPS IT IN A PAGER
         *
         */
        // This handles the possible search request from the grid
        if (!empty($_REQUEST['_search'])) self::_search($q);
        // Add order to this mess :)
        if (!empty($sidx) && !empty($sord)) $q->orderby($sidx . ' ' . $sord);
        // Set up doctrine page for pagnation
        $pager = new Doctrine_Pager($q, $currentPage, $resultsPerPage);
        // Execute the query
        Kohana::log('debug', "JGRID QUERY IS: " . $q->getSqlQuery());
        $results = $pager->execute(array() , Doctrine::HYDRATE_SCALAR);
        /**
         *
         * THIS SECTION BUILDS AN ARRAY THAT REPRESENTS A JSON RESPONSE TO THE GRID
         *
         */
        $encodeArray['page'] = $pager->getPage();
        $encodeArray['total'] = $pager->getLastPage();
        $encodeArray['records'] = $pager->getNumResults();
        // For each of our results start building an array we can use to generate json
        foreach($results as $result) {
            // Build a pointer to a new rows array
            $ptEncodeArray = & $encodeArray['rows'][];
            // Loop through each requested column
            foreach($this->query['columns'] as $hydrateName => $model) {
                // Load a new cell with the value from the query, or blank if empty
                $cell = & $ptEncodeArray['cell'][];
                $cell = empty($result[$hydrateName]) ? '' : $result[$hydrateName];
                if (is_string($cell)) {
                    $cell = htmlspecialchars($cell);
                }
                // Check if this field has a callback...
                if (!empty($this->query['callbacks'][$hydrateName])) $cell = self::_cellCallback($cell, $this->query['callbacks'][$hydrateName], $result);
                // Check if this field should be displayed as a link...
                if (!empty($cell) && !empty($this->query['links'][$hydrateName])) $cell = self::_cellToAnchor($cell, $this->query['links'][$hydrateName], $result);
            }
            if (!empty($this->query['actions'])) $ptEncodeArray['cell'][] = self::_cellActions($this->query['actions'], $result);
        }
        // Encode the results into a json object
        $json = json_encode($encodeArray);
        // These hacks let you pass an anchor in the json without it exploding....
        $json = str_replace('\/', '/', $json);
        $json = str_replace('\"', '', $json);
        // Return the json
        return $json;
    }
    /**
     * This function sorts the columns of a grid by an optional columnWeight parameter
     *
     * @return void
     */
    private function _orderColumns()
    {
        // Sort the columns array
        ksort($this->query['columns'], SORT_NUMERIC);
        // Move the inner arrays (now sorted by the outer array values) to the outer array
        // and ditch the subarray groups of columns by weight
        foreach($this->query['columns'] as $key => $columns) {
            // FIRST remove the old column (foreach is operating on a copy so it is saving the value)
            unset($this->query['columns'][$key]);
            // Move this inner array to the the outer column array
            $this->query['columns'] = arr::merge($this->query['columns'], $columns);
            // We also need the jquery colModel to reflect the same order so first get the colModel value
            $colModel = $this->jquery['colModel'][$key];
            // Unset the inner array
            unset($this->jquery['colModel'][$key]);
            // Set the outer array with what used to to be the inner array
            $this->jquery['colModel'] = arr::merge($this->jquery['colModel'], $colModel);
        }
        // This is an easy way to re-index the array (otherwise the grid create JS will be incorrect)
        $this->jquery['colModel'] = array_values($this->jquery['colModel']);
    }
    /**
     * This function will build a doctrine query object from the supplied columns
     *
     * @return Doctrine_Query
     */
    private function _autoQuery()
    {
        // Initiate a new doctring query
        $q = Doctrine_Query::create();
        // If we dont have a base model then there is nothing to do....
        if ($from = self::_implode($this->query['from'])) {
            // Add the from
            $q->from($from);
            /**
             *
             * THIS SECTION ADDS ANY MISSING COLUMNS FOR CALLBACKS OR LINKS TO THE QUERY
             *
             */
            if (!empty($this->query['additonalArgs'])) {
                // Go through each argument needed and make sure it is added to the query
                foreach($this->query['additonalArgs'] as $argument => $forWhat)
                // Add this to the column to the query if it doesnt exist without adding it to the grid
                self::add($argument, $argument, NULL, false, false);
            }
            /**
             *
             * THIS SECTION LOADS ARBITRAY DOCTRINE METHODS FROM THE QUERY ARRAY
             *
             */
            foreach($this->doctrineMethods as $doctrineMethod) {
                if (!empty($this->query[$doctrineMethod])) {
                    if (stristr($doctrineMethod, 'where')) {
                        foreach($this->query[$doctrineMethod] as $statement) {
                            kohana::log('debug', 'Adding: ' . $doctrineMethod .'(' .$statement['condition'] .',' . $statement['parameter'] .')');
                            $q->$doctrineMethod($statement['condition'], $statement['parameter']);
                        }
                    } else if ($params = self::_implode($this->query[$doctrineMethod])) {
                        $q->$doctrineMethod($params);
                    }
                }
            }
        }
        return $q;
    }
    /**
     * This function adds the actions to the last column
     *
     * @return string HTML of actions
     * @param array|string $actions The actions
     * @param array $result The current result row
     */
    private function _cellActions($actions, $result)
    {
        foreach($actions as $name => $action) {
            // Unless the user overrides this then do not put an action link up if there is nothing
            // to work against (ie no edit user if no user_id)
            $reqAllArgs = isset($action['reqAllArgs']) ? $action['reqAllArgs'] : true;
            // This catches cellToAnchor if it returns false and skips adding the action link
            if ($html = self::_cellToAnchor($name, $action, $result, $reqAllArgs)) $cell[] = $html;
        }
        return self::_implode($cell, '<span class="action_seperator">|</span>');
    }
    /**
     * This function replaces the cell value with the return of a users function (if callable)
     * and can pass any arbitrary arguments.
     *
     * @return string a new value for the cell
     * @param string $cell The current value of the cell
     * @param object|array $callback This the the callback
     * @param array $result This result set, used to extract parameters for the user function
     */
    private function _cellCallback($cell, $callback, $result)
    {
        // Get the callback
        $function = empty($callback['function']) ? $callback : $callback['function'];
        // Check if this is a valid method
        if (self::_callback($function)) {
            // set the current cell value as the first argument
            $arguments[] = $cell;
            // Tack on any argumnets
            if (!empty($callback['arguments'])) {
                // Standardize strings as arrays
                $callback['arguments'] = is_array($callback['arguments']) ? $callback['arguments'] : array(
                    $callback['arguments']
                );
                // Build up the argument array with any additional arguments
                foreach($callback['arguments'] as $argument) {
                    $hydrateName = self::_splitPath($argument, 'hydrateName');
                    $arguments[] = isset($result[$hydrateName]) ? $result[$hydrateName] : '';
                }
            }
            // Attempt to call the user function
            try {
                return call_user_func_array($function, $arguments);
            }
            catch(Exception $e) {
            }
        }
        // If something goes wrong make sure we return the orginal cell value
        return htmlspecialchars($cell);
    }
    /**
     *
     * This function takes the result cell, the link parameters, and the result row
     * and puts it all together to wrap a cell in a appropriate link!
     *
     * @return string HTML of cell wrapped in link
     * @param string $cell The current value of the cell (to be wrapped)
     * @param array $link The link parameter array
     * @param array $result The current result row
     */
    private function _cellToAnchor($cell, $link, $result, $reqAllArgs = false)
    {
        // Ensure there is something for a url
        if (!empty($link['link'])) $url = $link['link'];
        elseif (is_string($link)) $url = $link;
        else $url = '';
        $cell = self::_i18n($cell);
        //  Ensure attributes and arguments are set so we are not dealing with unset vars
        $attributes = empty($link['attributes']) ? '' : $link['attributes'];
        $arguments = array();
        // This ensures there is a title tag on the link
        if (empty($attributes['title'])) $attributes['title'] = inflector::underscore($cell);
        // Tack on any argumnets
        if (!empty($link['arguments'])) {
            // Standardize strings as arrays
            $link['arguments'] = is_array($link['arguments']) ? $link['arguments'] : array(
                $link['arguments']
            );
            foreach($link['arguments'] as $argument) {
                extract(self::_splitPath($argument));
                // If we can not fulfill the arguments (ie there was no result for an argument)
                // and we are required to do so then return false
                if (!isset($result[$hydrateName]) && $reqAllArgs) return false;
                // This looks for the optional passAsSegments parameter for a column link
                if (!isset($link['passAsSegments']) || $link['passAsSegments'] == true) {
                    // If the args are passed as segments then we need to maintain the total count and order
                    $arguments[] = isset($result[$hydrateName]) ? $result[$hydrateName] : '';
                } else {
                    // If we are passing these args a GETs then we only need to do those that are set
                    if (isset($result[$hydrateName])) $arguments[] = $column . '=' . $result[$hydrateName];
                }
            }
        }
        // If arguments are passed as segments then the seperator is / and there will ALWAYS be a set amount
        if (!isset($link['passAsSegments']) || $link['passAsSegments'] == true) {
            $url = rtrim($url, '/') . '/' . rawurlencode(self::_implode($arguments, '/'));
        } else {
            // If the arguments are GETs then join on &, if there are any
            if ($args = self::_implode($arguments, '&')) $url.= '?' . $args;
        }
        $cell = htmlspecialchars($cell);
        // Wrap the cell results in a anchor tag
        return html::anchor($url, $cell, $attributes);
    }
    /**
     *
     * This function will formate a conditional statement for the query based on
     * a response from the grid (user)
     *
     * TODO: THIS IS CURRENTLY A SEVERE SECURITY RISK!!!! Unless doctrine is escaping SQL, or maybe Kohana
     *
     * @return void
     * @param Doctrine_Query $q The doctrine query that the where clause should be applied to
     */
    private function _search($q)
    {
        // Be sure all the necessary elements exists
        if (!empty($_REQUEST['searchField']) && !empty($_REQUEST['searchOper']) && !empty($_REQUEST['searchString'])) {
            // Get the search criteria
            $searchField = $_REQUEST['searchField'];
            $searchOper = $_REQUEST['searchOper'];
            $searchString = $_REQUEST['searchString'];
            // Build an array to link jqgrid short hand to actual sql operators
            $convertOper = array(
                'bw' => 'LIKE ?',
                'eq' => '= ?',
                'lt' => '< ?',
                'le' => '<= ?',
                'gt' => '> ?',
                'ge' => '>= ?',
                'ew' => 'LIKE ?',
                'cn' => 'LIKE  ?'
            );
            // If we are building a LIKE clause then append a % where appropriate
            switch ($searchOper) {
            case 'cn':
                $searchString = '%' . $searchString . '%';
                break;

            case 'ew':
                $searchString = '%' . $searchString;
                break;

            case 'bw':
                $searchString = $searchString . '%';
                break;
            }
            // Add this where clause to the query
            $q->addWhere($searchField . ' ' . $convertOper[$searchOper], $searchString);
        }
    }
    /**
     * This function standardizes the handling of all doctrine where clause additions
     *
     * @return jgrid
     * @param string $path The path model/column to the column to operate on
     * @param string $operator The operator to apply to the path
     * @param string|array $parameter[optional] A doctrine style parameter to test against
     * @param string $where[optional] The type of doctrine where clause to add
     */
    private function _where($path, $operator, $parameter = '~NULL~', $where = 'addWhere')
    {
        extract(self::_splitPath($path));
        $condition = $modelAlias . '.' . $column . ' ' . $operator;
        $skipQuest = array(
            'whereIn',
            'andWhereIn',
            'orWhereIn',
            'whereNotIn',
            'andWhereNotIn',
            'orWhereNotIn'
        );
        if (!in_array($where, $skipQuest)) {
            if (!strstr($operator, '?') && !strstr($parameter, '~NULL~')) $condition.= ' ?';
        }
        $this->query[$where][] = array(
            'condition' => $condition,
            'parameter' => $parameter
        );
    }
    /**
     * This function adds the additional arguments to the query if they dont exist when the query
     * is executed without them showing up in the grid.  Used to fulfill arguments for links, actions,
     * and callbacks.
     *
     * @return void
     * @param array|string $arguments List of paths of arguments need
     * @param string $forWhat[optional] Not used anywhere but it should be set to something....
     */
    private function _additionalArg(&$arguments, $forWhat = '')
    {
        // Build an array of columns that the callbacks need so we can ensure they are inclued in the query
        if (!empty($arguments)) if (is_array($arguments)) {
            foreach($arguments as $argument) $this->query['additonalArgs'][$argument] = $forWhat;
        } else {
            $this->query['additonalArgs'][$arguments] = $forWhat;
        }
    }
    /**
     * This function handles splitting all the mode/field paths
     * Retrieved from Explorer.php on 6/19/2009 by K Anderson and modified
     *
     * TODO: It should be possible to get deeper recursion with more complexe paths User/UserParameters/parameter_id
     *
     * @return string|array depending on segment
     * @param string $path The path to operate on
     * @param string $segment[optional] If present and valid the the requested segment is returned
     */
    private function _splitPath($path, $segment = '')
    {
        // Clean up and split the path on the DELIM
        $path = trim($path, DELIM);
        $path = explode(DELIM, $path);
        // If we where provided a path that split then it represents a model/field
        if (count($path) == 2) {
            // The model is the left most string of path after split on DELIM
            $model = $path[0];
            // Set the model alias to use for this column
            $modelAlias = self::_getAlias($path[0]);
            // The column is the right most string of path after split on DELIM
            $column = $path[1];
            // If we recieved a path with no DELIM assume it is in reference to baseModel
            
        } else {
            // The model is the baseModel
            $model = $this->baseModel;
            // The alias is the base model alias
            $modelAlias = $this->baseAlias;
            // The column is the supplied by path
            $column = $path[0];
        }
        // If the column contains a '.' then it is a full column name and
        // need special treatment.... Used primarily for customizing the query
        if (substr($column, 1, 1) == '.') {
            $dqlColumn = $column;
            $column = substr_replace($column, '', 0, 2);
            $modelAlias = substr($dqlColumn, 0, 1);
            $hydrateName = $modelAlias . '_' . substr($dqlColumn, 2);
        } else {
            // Build the hydrate name here as a convience
            $hydrateName = $modelAlias . '_' . $column;
            // Build the doctrine query languange column name
            $dqlColumn = $modelAlias . '.' . $column;
        }
        // Returns just the segment or all the segment vars
        if (isset($$segment)) return $$segment;
        else return compact('model', 'modelAlias', 'column', 'hydrateName', 'dqlColumn');
    }
    /**
     * This function does the same as implode except it returns false if
     * the passed pieces isnt set or doesnt have any value.  It can also
     * handle strings without killing small puppies...
     *
     * @return string or bool False if there is not result or string of imploded pieces
     * @param string or array $pieces
     * @param string $glue[optional]
     */
    public function _implode(&$pieces, $glue = ',')
    {
        if (empty($pieces)) return false;
        elseif (is_string($pieces)) return $pieces;
        elseif (count($pieces) == 1) return reset($pieces);
        else return implode($glue, $pieces);
    }
    /**
     * This finds a alias for a model name, and resolves any collisions
     *
     * @return char
     * @param string $model
     */
    private function _getAlias($model)
    {
        // If this model has already been assigned an alias return it
        if (!empty($this->query['model'][$model])) return $this->query['model'][$model];
        // Build and array of 'used' aliases
        $existingAlias = empty($this->query['model']) ? array() : array_values($this->query['model']);
        // Include the base alias when checking for collisions
        $existingAlias[$this->baseAlias] = '';
        // Attempt to find an unused char in the model name to use as the alias
        foreach(str_split($model) as $alias) {
            $alias = strtolower($alias);
            if (!array_key_exists($alias, $existingAlias)) {
                $this->query['model'][$model] = $alias;
                return $alias;
            }
        }
        // If amazingly there were no chars in the model name that didnt have a collision then use
        // the whole alphabet (HIGHLY unlikely)
        $possibleAlias = array(
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z'
        );
        $avaliableAlias = array_diff($possibleAlias, $existingAlias);
        return $this->query['model'][$model] = reset($avaliableAlias);
    }
    /**
     * This fucntion determines if a user function is callable taking visibility into account
     * Retrieved from http://nz.php.net/manual/en/function.is-callable.php on 6/20/2009 by K Anderson
     *
     * @return bool true if the function is callable otherwise false
     * @param object $var The function under test
     */
    private function _callback($var)
    {
        if (is_array($var) && count($var) == 2) {
            $var = array_values($var);
            if ((!is_string($var[0]) && !is_object($var[0])) || (is_string($var[0]) && !class_exists($var[0]))) {
                return false;
            }
            $isObj = is_object($var[0]);
            $class = new ReflectionClass($isObj ? get_class($var[0]) : $var[0]);
            if ($class->isAbstract()) {
                return false;
            }
            try {
                $method = $class->getMethod($var[1]);
                if (!$method->isPublic() || $method->isAbstract()) {
                    return false;
                }
                if (!$isObj && !$method->isStatic()) {
                    return false;
                }
            }
            catch(ReflectionException $e) {
                return false;
            }
            return true;
        } elseif (is_string($var) && function_exists($var)) {
            return true;
        }
        return false;
    }
    private function _i18n($displayName)
    {
        return __($displayName);
    }
    private function _renderRESTfulErrors()
    {
        kohana::$instance->template->content = new View('blank');
        kohana::$instance->template->content->context = 'test';
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));
    }
}
