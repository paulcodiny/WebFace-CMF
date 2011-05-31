<?php

/**
 * Description of WfUtils
 *
 * @author павел
 */
class WfUtils {
	public static function makeClassName($string, $prefix = 'Wf') {
		$className = '';
		// true because we capitalize the first letter
		$capitalizeNext = true;
		for ($i = 0, $n = strlen($string); $i < $n; $i++) {
			if ($string[$i] == '_') {
				$capitalizeNext = true;
				continue;
			}
			
			if ($capitalizeNext) {
				$className .= ucfirst($string[$i]);
				$capitalizeNext = false;
			} else {
				$className .= $string[$i];
			}
		}
		
		if (strpos($className, $prefix) === false) {
			$className = $prefix . $className;
		}
		
		return $className;
	}
}