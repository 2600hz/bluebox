<?php defined('SYSPATH') or die('No direct access allowed.');
class Directorygrouping_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'directory';
    public static $displayName = 'Directory Listing';
    public static $author = 'Jort Bloem';
    public static $vendor = 'BTG';
    public static $license = 'MPL';
    public static $summary = 'Shows a public-facing directory listing';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array( 
		'core' => 0.1, 
		'jstree' => 1.0, 
		'sofia'=> 1.0);
    public static $navBranch = '/Organization/';
    public static $navURL = '/directory';
    public static $navSubmenu = array (
	'listing'=>'/directory',
	'arrange'=>'/directory/arrange'
   );

    public function postInstall()
    {
        $tree=Doctrine::getTable('Grouping')->getTree();

        $root=new Grouping;
        $root->name='Root';
        $root->locked=true;
        $root->save();

        $tree->createRoot($root);

        $branches=new Grouping;
        $branches->name="Branches";
        $branches->locked=true;
        $branches->save();
        $branches->getNode()->insertAsLastChildOf($root);

        foreach (explode(" ","Albany Christchurch Ellerslie Remote") AS $name) {
                $branch=new Grouping;
                $branch->name=$name;
                $branch->save();
                $branch->getNode()->insertAsLastChildOf($branches);
        }
	// the last branch shows whatever is happening on remotebluebox
	//$branch['plugins']=array('directory'=>array('remote'=>'http://remotebluebox/directory/xmlout'));
	$branch->save();
    }

    //Add our devices to the database
    public function repair() {
        parent::repair();
        Doctrine::createTablesFromArray(array('Grouping'));
    }

}

