<?php

PHPWS_Core::initModClass('nomination', 'exception/ViewException.php');

class ViewNotFoundException extends ViewException {

    public function __construct($message, $code = 0){
        parent::__construct($message, $code);
    }
}
