<?php defined('SYSPATH') or die('No direct access allowed.');

class xml
{
        /**
        * Converts a simpleXML element into an array. Preserves attributes and everything.
        * You can choose to get your elements either flattened, or stored in a custom index that
        * you define.
        * For example, for a given element
        * <field name="someName" type="someType"/>
        * if you choose to flatten attributes, you would get:
        * $array['field']['name'] = 'someName';
        * $array['field']['type'] = 'someType';
        * If you choose not to flatten, you get:
        * $array['field']['@attributes']['name'] = 'someName';
        * _____________________________________
        * Repeating fields are stored in indexed arrays. so for a markup such as:
        * <parent>
        * <child>a</child>
        * <child>b</child>
        * <child>c</child>
        * </parent>
        * you array would be:
        * $array['parent']['child'][0] = 'a';
        * $array['parent']['child'][1] = 'b';
        * ...And so on.
        * _____________________________________
        * @param simpleXMLElement $xml the XML to convert
        * @param boolean $flattenValues    Choose wether to flatten values
        *                                    or to set them under a particular index.
        *                                    defaults to true;
        * @param boolean $flattenAttributes Choose wether to flatten attributes
        *                                    or to set them under a particular index.
        *                                    Defaults to true;
        * @param boolean $flattenChildren    Choose wether to flatten children
        *                                    or to set them under a particular index.
        *                                    Defaults to true;
        * @param string $valueKey            index for values, in case $flattenValues was set to
        *                            false. Defaults to "@value"
        * @param string $attributesKey        index for attributes, in case $flattenAttributes was set to
        *                            false. Defaults to "@attributes"
        * @param string $childrenKey        index for children, in case $flattenChildren was set to
        *                            false. Defaults to "@children"
        * @return array the resulting array.
        */
        public static function simpleXMLToArray($xml,
                        $flattenValues=true,
                        $flattenAttributes = true,
                        $flattenChildren=true,
                        $valueKey='@value',
                        $attributesKey='@attributes',
                        $childrenKey='@children'){

                $return = array();
                if(!($xml instanceof SimpleXMLElement))
                        return $return;
                $name = $xml->getName();
                $_value = trim((string)$xml);
                if(strlen($_value)==0)
                        $_value = null;

                if($_value!==null) {
                        if(!$flattenValues)
                                $return[$valueKey] = $_value;
                        else
                                $return = $_value;
                }

                $children = array();
                $first = true;
                foreach($xml->children() as $elementName => $child){
                        $value = self::simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
                        if(isset($children[$elementName])){
                                if($first){
                                        $temp = $children[$elementName];
                                        unset($children[$elementName]);
                                        $children[$elementName][] = $temp;
                                        $first=false;
                                }
                                $children[$elementName][] = $value;
                        }
                        else
                                $children[$elementName] = $value;
                }
                if(count($children)>0){
                        if(!$flattenChildren)
                                $return[$childrenKey] = $children;
                        else
                                        $return = array_merge($return,$children);
                }

                $attributes = array();
                foreach($xml->attributes() as $name=>$value)
                        $attributes[$name] = trim($value);
                if(count($attributes)>0){
                        if(!$flattenAttributes)
                                $return[$attributesKey] = $attributes;
                        else
                                $return = array_merge($return, $attributes);
                }
                if (count($return) == 0)
                        return '';
                return $return;
        }

        public static function xmlstrtoarr($xmlstr)
        {
                $sxml = new simpleXMLElement($xmlstr);
                return self::simpleXMLToArray($sxml);
        }
}
?>