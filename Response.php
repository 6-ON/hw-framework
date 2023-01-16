<?php

namespace app\core;

class Response
{
    public const TYPE_JSON = 'application/json';
    public const TYPE_JS = 'application/javascript';
    //TODO: adding more types
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function setContentType($type)
    {
        header('Content-type: ' .$type);
    }
    public function redirect(string $url)
    {
        header('Location: '.$url);
    }
}