<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 1.0.0
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
if(! function_exists('is_serialized') ): 
	function is_serialized( $data, $strict = true ) {
		// if it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}
endif;

/**
 * Serialize data, if needed.
 *
 * @since 1.0.0
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
if(! function_exists('maybe_serialize') ):
	function maybe_serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) && false == is_serialized( $data ) )
			return serialize( $data );
	
		return $data;
	}
endif;

/**
 * Unserialize value only if it was serialized.
 *
 * @since 1.0.0
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
if(! function_exists('maybe_unserialize') ):
	function maybe_unserialize( $original ) {
		if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
			return @unserialize( $original );
		return $original;
	}
endif;


/**
 * Check option, exists or not.
 *
 * If $option does not exists, then returned value will always be false.
 *
 * @since 1.0.0
 *
 * @param string   $option Optional. Default false.
 * @return bool False if not exists and true if it is.
 */

if(! function_exists('have_option') ):
	function have_option( $option = false )
	{
		
		$option = trim( $option );
		if ( empty( $option ))
			return false;
		
		$CI =& get_instance();
		$CI->load->database();

		$CI->db->select( 'option_name' );
		$CI->db->where( 'option_name', $option );
		$CI->db->from( 'options' );
		$query = $CI->db->get();
			if( $query->num_rows() > 0 ):
				return true;
			else:
				return false;
			endif;			
	}
endif;



/**
 * Add a new option.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database. Remember,
 * resources can not be serialized or added as an option.
 *
 * You can create options without values and then update the values later.
 * Existing options will not be updated.
 *
 * @since 1.0.0
 *
 *
 * @param string         $option      Name of option to add.
 * @param mixed          $value       Optional. Option value. Must be serializable if non-scalar.
 * @param string|bool    $autoload    Optional. Whether to load the option when Project starts up.
 *                                    Default is enabled. Accepts 'no' to disable for legacy reasons.
 * @return bool False if option was not added and true if option was added.
 */

if( ! function_exists( 'add_option' ) ):
	function add_option($option = false, $value = false)
	{
		
		$option = trim( $option );
		if ( empty( $option ) || have_option( $option ) == true )
			return false;

		$CI =& get_instance();
		$CI->load->database();

		$data['option_name'] = $option;
		$data['option_value'] = maybe_serialize( $value );
		$data['autoload'] = true;
		
		$CI->db->insert( 'options', $data );

		return true;
	}
endif;


/**
 * Retrieve option value based on name of option.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false.
 *
 * If the option was serialized then it will be unserialized when it is returned.
 *
 * @since 1.0.0
 *
 *
 * @param string $option  Name of option to retrieve.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @return mixed Value set for the option.
 */
 
 if(! function_exists('get_option') ):
	function get_option( $option = false, $default = false )
	{
		
		$option = trim( $option );
		if ( empty( $option ))
			return false;
			
		$CI =& get_instance();
		$CI->load->database();

		if( have_option( $option ) ):
			$CI->db->select( 'option_value' );
			$CI->db->where( 'option_name', $option );
			$CI->db->from( 'options' );
			$query = $CI->db->get();
			$qry = $query->result();
			$value = $qry[0]->option_value;
			$value = ( is_serialized( $value ) ) ? maybe_unserialize( $value ) : $value;
			return $value;						
		else:
			return $default;
		endif;
	}

endif;



/**
 * Update the value of an option that was already added.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database.
 *
 * If the option does not exist, then the option will be added with the option value,
 * with an `$autoload` value of 'yes'.
 *
 * @since 1.0.0
 *
 *
 * @param string      $option   Option name.
 * @param mixed       $value    Option value. Must be serializable if non-scalar.
 * @param string|bool $autoload Optional. Whether to load the option when Project starts up. For existing options,
 * `$autoload` can only be updated using `update_option()` if `$value` is also changed.
 *  Accepts 'yes'|true to enable or 'no'|false to disable. For non-existent options,
 *  the default value is 'yes'. Default null.
 * @return bool False if value was not updated and true if value was updated.
 */

if( ! function_exists( 'update_option' ) ):
	function update_option($option = false, $value = false)
	{
		
		$option = trim($option);
		if ( empty($option) )
			return false;

		$CI =& get_instance();
		$CI->load->database();
		
		if( have_option( $option ) ):

			$data['option_name'] = $option;
			$data['option_value'] = maybe_serialize( $value );
			$data['autoload' ] = true;

			$CI->db->where('option_name', $option);
			$CI->db->update('options', $data);
			return true; 
		else:
			add_option( $option, $value );
			return true;
		endif;
	}
endif;

/**
 * Removes option by name.
 *
 * @since 1.0.0
 *
 *
 * @param string $option Name of option to remove.
 * @return bool True, if option is successfully deleted. False on failure.
 */
function delete_option( $option ) {

	$option = trim( $option );
	if ( empty( $option ) )
		return false;
		
		$CI =& get_instance();
		$CI->load->database();
		
		if( $CI->db->delete('options', array('option_name' => $option) ) && have_option( $option ) )
			return true;
		
		return false;
}

/**
 * return excerpt of quote based on characters.
 *
 * @since 1.0.0
 *
 *
 * @param string $text string content.
 * @param integer $chars charcater limit. 
 * @return string , return string from index 0 to limit set by user, default 25.
 */
//
if(!function_exists('excerpt')) {
	function excerpt($text = '', $chars = 25) {
		$text = $text." ";
		$text = substr($text,0,$chars);
		$text = substr($text,0,strrpos($text,' '));
		$text = $text."...";
		return $text;
	}
}

