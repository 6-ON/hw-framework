<?php

namespace app\core\exception;

class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'The page you are looking for was not found.';
}