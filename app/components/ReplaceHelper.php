<?php

/**
 * Replace strings in mixed objects
 */
class ReplaceHelper
{
	/**
	 * Recursive replace values
	 * @param string|array|object $data
	 * @param array   $to_replace   array of (search,replace) pairs
	 * @param boolean $serialized
	 * @param boolean $parent_serialized
	 * @return string
	 */
	public static function recursiveReplace($data, $to_replace, $serialized = false, $parent_serialized = false)
	{
		$is_json = false;
		if ( is_string($data) && ( $unserialized = @unserialize($data) ) !== false ) {
			// PHP currently has a bug that doesn't allow you to clone the DateInterval / DatePeriod classes.
			// We skip them here as they probably won't need data to be replaced anyway
			if ( is_object($unserialized) && ( $unserialized instanceof DateInterval || $unserialized instanceof DatePeriod ) ) {
				return $data;
			}
			$data = self::recursiveReplace($unserialized, $to_replace, true, true);
		}
		elseif ( is_array($data) ) {
			$_tmp = array();

			foreach ( $data as $key => $value ) {
				$_tmp[$key] = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
		}
		elseif ( is_object($data) ) {
			$_tmp = clone $data;

			foreach ( $data as $key => $value ) {
				$_tmp->$key = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
		}
		elseif ( is_json($data, true) ) {
			$_tmp = array();
			$data = json_decode($data, true);

			foreach ( $data as $key => $value ) {
				$_tmp[$key] = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
			$is_json = true;
		}
		elseif ( is_string($data) ) {
			$data = self::replace($data, $to_replace);
		}

		if ( $serialized )
			return serialize($data);

		if ( $is_json )
			return json_encode($data);

		return $data;		
	}
	
	/**
	 * Apply replace
	 * @param string $subject
	 * @return boolean
	 */
	public static function replace( $subject, $to_replace )
	{
		if ( empty($to_replace) || !is_array($to_replace) ) {
			return $subject;
		}
		
		foreach ( $to_replace as $params ) {
			$subject = str_ireplace($params[0], $params[1], $subject);
		}
		return $subject;
	}
}
