<?php defined('SYSPATH') or die('No direct access allowed.');
class Grouping extends Bluebox_Record
{
        public function setTableDefinition()
        {
                $this->hasColumn('grouping_id','integer',11,array('unsigned'=>true,'primary'=>true,'autoincrement'=>true));
                $this->hasColumn('name', 'string', 255);
                $this->hasColumn('locked','boolean',null,array('default'=>false));
        }
    
        public function setUp()
        {
                $this->actAs("NestedSet");
		$this->actAs('GenericStructure');
        }


// All the jsTree functions (which should be from here) should be in their own library, and $this->actAs('jsTree');
// Unfortunately, in the jsTree module, it can't seem to access functions from NestedSet, e.g. $this->getNode().
        public function jsTree_controller($operation,$params,$debug=false)
        {
                $this->jsTree_header($debug);
                if ($operation=='get_children') {
                        print json_encode($this->jsTree_get_children());
                } elseif ($operation=='create_node') {
                        print json_encode($this->jsTree_create_node($params['title']));
                } elseif ($operation=='remove_node') {
                        print json_encode($this->jsTree_remove_node());
                } elseif ($operation=='rename_node') {
                        print json_encode($this->jsTree_rename_node($params['title']));
                } elseif ($operation=='move_node') {
                        print json_encode($this->jsTree_move_node($params['ref'],$params['position'],$params['copy']));
                } else {
                        return;
                }
                exit;
        }
        public function jsTree_header($debug=false)
        {
                if ($debug) {
                        print "<pre>\n";
                } else {
                        header("HTTP/1.0 200 OK");
                        header('Content-type: text/json; charset=utf-8');
                        header("Cache-Control: no-cache, must-revalidate");
                        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                        header("Pragma: no-cache");
                }
        }
        public function jsTree_rename_node($title)
        {
                $this->name=$title;
                $this->save();
                return array("status"=>1);
        }
        
        public function jsTree_move_node($newparent,$position,$copy)
        {
                if ($copy==1) {
                        return array("status"=>0);
                }
		if (substr($newparent,0,5)=='node_') {
			$newparent=substr($newparent,5);
		}
                $newparent=Doctrine::getTable(get_class($this))->findOneBy('grouping_id',$newparent);
                if ($position==0) {
                        $this->getNode()->moveAsFirstChildOf($newparent);
                } else {
                        $siblings=$newparent->getNode()->getChildren();
                        $this->getNode()->moveAsNextSiblingOf($siblings[$position-1]);
                }
                return array("status"=>1);
        }
        public function jsTree_remove_node()
        {
                $this->getNode()->delete();
                return array("status"=>1);
        }
        public function jsTree_create_node($title)
        {
                $classname=get_class($this);
                $new=new $classname();
                $new->name=$title; 
                $new->save();
                $new->getNode()->insertAsLastChildOf($this);
                return array("status"=>1,"id"=>"node_".$new->grouping_id);
        }
        public function jsTree_get_children()
        {
                $stack=array(array(
                                'attr'=>array("id"=>"node_".$this->grouping_id,"rel"=>"default","locked"=>$this->locked),
                                'data'=>$this->name,
                        ));
                foreach ($this->getNode()->getDescendants() AS $desc) {
                        $new=array(
                                'attr'=>array("id"=>"node_".$desc->grouping_id,"rel"=>"default","locked"=>$desc->locked),
                                'data'=>$desc->name,
                        );
                        if (array_key_exists('children',$stack[$desc->level-1])) {
                                array_push($stack[$desc->level-1]["children"],$new);
                        } else {
                                $stack[$desc->level-1]["children"]=array($new);
                                $stack[$desc->level-1]["state"]="open";
                        }
                        $stack[$desc->level]=&$stack[$desc->level-1]["children"][count($stack[$desc->level-1]["children"])-1];
                }
                return array($stack[0]);
        }

}

