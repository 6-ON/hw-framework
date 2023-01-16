<?php

namespace sixon\hwFramework\exception;

class ForbiddenException extends \Exception
{
    protected $message = 'Permission Denied';
    protected $code = 403;
}