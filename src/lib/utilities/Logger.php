<?php
/******************************************
 ***** Logger Static Class Definition *****
 ******************************************/
class Logger {
	const DEBUG = "DEBUG";
	const WARN = "WARN";
	const ERROR = "ERROR";
	const FATAL = "FATAL";
	
	public static function write($level = Logger::DEBUG, $message = "", $exception = null) {
		$toWrite = "";
		
		//validate error level
		if($level != Logger::DEBUG && $level != Logger::WARN
			&& $level != Logger::ERROR && $level != Logger::FATAL) {
			//If an invalid error level is specified, set the error level to ERROR.
			//We don't want to set the level to DEBUG or WARN because we need to make sure that we are aware that a relatively severe error has occured (that an incorrect invocation of this method has been committed)
			//We don't want to set the level to FATAL because we don't want writing to the error log to inappropriately disturb the user's browsing.
			$level = Logger::ERROR . " (Notice: invalid call to Logger::write())";
		} //end IF
		
		//validate message
		if(!is_string($message)) {
			$level = Logger::ERROR . " (Notice: invalid call to Logger::write())";
			$message = "";
		} //end IF
		
		//begin error log message text
		$toWrite = "Severity: $level;  Message: $message;  ";
		
		//determine exception class's info if a valid one exists
		$exceptionClass = "";
		if(is_object($exception) && $exception instanceof Exception) $exceptionClass = get_class($exception);
		if($exceptionClass != "") $toWrite .= "Exception thrown: $exceptionClass;  Exception Message: {$exception->getMessage()}; Exception Code: {$exception->getCode()};  File: {$exception->getFile()};  Line: {$exception->getLine()};";
		
		//write message to error log
		error_log($toWrite);
		
		//if error level == FATAL, send an email to the admin and forward the user to the fatal error page
		if($level == Logger::FATAL) {
			/* TODO: SEND EMAIL */
			header("Location: " . FATAL_ERROR_PAGE);
			exit;
		} //end if
	} //end static method write()
} //end class Logger	
?>