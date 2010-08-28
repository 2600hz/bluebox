<?php


/**
 * Project: RainTPL, compile HTML template to PHP.
 *  
 * File: raintpl.class.php
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://www.raintpl.com
 * @author Federico Ulfo <info@rainelemental.net>
 * @version 1.7.5
 * @copyright 2006 - 2009 Federico Ulfo | www.RainElemental.net
 * @package RainTPL
 */


 
/**
 * 
 *Cache enabled:
 * TRUE improve speed
 * FALSE recompile template each executions
 * 
 */

define( "TPL_CACHE_ENABLED", true );


/**
 * Extension of template
 *
 */

define( "TPL_EXT", "html" );



/**
 * Questa costante serve per la sicurezza dei template, in modo che un template non pu˜ essere eseguito senza questa classe.
 *
 */
	
define( "IN_RAINTPL", true );



/**
 * RainTPL Template class.
 * Questa classe permette di caricare e visualizzare i template
 * 
 * @access public
 * 
 */

class RainTPL{
	
	/**
	 * Contiene tutte le variabili assegnate al template
	 * @access private
	 * @var array
	 */
	var $variables = array( );
	

	/**
	 * Directory dove sono i templates
	 * @access private
	 * @var string
	 */
	var $tpl_dir = "themes/";
	
	
	
	/**
	 * Inizializza la classe. 
	 *
	 * @param string $tpl_dir Setta la directory da cui prendere i template. E' sufficente settarla al primo utilizzo del template engine
	 * @return RainTPL
	 */

	function RainTPL( $tpl_dir = null ){

		if( $tpl_dir )
			$this->tpl_dir = $GLOBALS[ 'RainTPL_tpl_dir' ] = $tpl_dir . ( substr($tpl_dir,-1,1) != "/" ? "/" : "" );
		elseif( isset( $GLOBALS[ 'RainTPL_tpl_dir' ] ) )
			$this->tpl_dir = $GLOBALS[ 'RainTPL_tpl_dir' ];
			
	}
		
	/**
	 * Assign variable and name, or you can assign associative arrays variable to the template.
	 *
	 * @param mixed $variable_name Name of template variable
	 * @param mixed $value value assigned to this variable
	 */
	
	function assign( $variable, $value = null ){

		if( is_array( $variable ) )
			foreach( $variable as $name => $value )
				$this->variables[ $name ] = $value;
		elseif( is_object( $variable ) ){
			$variable = get_object_vars( $variable );
			foreach( $variable as $name => $value )
				$this->variables[ $name ] = $value;
		}
		else
			$this->variables[ $variable ] = $value;
	}
	
	

	/**
	 * If return_string == false, echo the template with all assigned variables as a string, else return the template as a string.
	 * 
	 * An appropriate use of this function is to store the result into a variable to bufferize or store the template.
	 * 
	 * Example:
	 * $tpl->draw( $tpl_name );
	 * 
	 * or
	 *
	 * echo $tpl->draw( $tpl_name, TRUE );
	 *
	 * @param string $tpl_name Nome del template da caricare
	 * @return string
	 */
	
	function draw( $tpl_name, $return_string = false ){

		if( count( $a = explode('/', $tpl_name) ) > 1 ){
			$temp = $tpl_name;
			$tpl_name = end( $a );	
			$tpl_dir = substr( $temp, 0, strlen($temp) - strlen( $tpl_name ) );
		}
		else
			$tpl_dir = null;

		//var is the variabile che si trova in ogni template
		$var = $this->variables;

		if( !file_exists( $template_file = $this->tpl_dir . $tpl_dir . $tpl_name . '.' . TPL_EXT ) ){
			trigger_error( "Template not found: $tpl_name" );
			if( $return_string )
				return "<div style=\"background-color:#f8f8ff; border: 1px solid #aaaaff; padding:10px;\">Template <b>$tpl_name</b> not found</div>";
			else{
				echo "<div style=\"background-color:#f8f8ff; border: 1px solid #aaaaff; padding:10px;\">Template <b>$tpl_name</b> not found</div>";
				return null;
			}
		}
		elseif( !is_writable( $this->tpl_dir ) )
			$compiled_filename = $this->tpl_dir . $tpl_dir . "/compiled/" . $tpl_name . "_def.php";
		elseif( TPL_CACHE_ENABLED && file_exists( $this->tpl_dir . $tpl_dir . "/compiled/" . $tpl_name . "_" . ( $filetime = filemtime( $template_file ) ) . ".php" ) )
			$compiled_filename = $this->tpl_dir . $tpl_dir . "/compiled/" . $tpl_name . "_" . $filetime . ".php";
		else{
			include_once "rain.tpl.compile.class.php";
			$RainTPLCompile_obj = new RainTPLCompile( );
			$RainTPLCompile_obj->compileFile( $tpl_name, $this->tpl_dir . $tpl_dir );
			// template last update date
			$filetime = filemtime( $this->tpl_dir . $tpl_dir . '/' . $tpl_name . '.' . TPL_EXT );
			$compiled_filename = $this->tpl_dir . $tpl_dir . "/compiled/" . $tpl_name . "_" . $filetime . ".php";
		}			



		
		//if return_string is true, the function return the output as a string
		if( $return_string ){
			ob_start();
			include( $compiled_filename );		
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;		
		}
		else
			include( $compiled_filename );
		
	}
		

}



?>