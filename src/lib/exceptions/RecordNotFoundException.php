<?php
class RecordNotFoundException extends Exception {
    public function __construct($errstr = NULL, $errcode = 0) {
        parent::__construct($errstr, $errcode);
    } // end method __construct()
} // end class RecordNotFoundException
?>