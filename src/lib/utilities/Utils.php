<?php
	class Utils {
		public static function sanitizeInput($text) {
			return trim(htmlentities(strip_tags(stripslashes($text))));
		} //end static method sanitizeInput()
	} //end class Utils
?>