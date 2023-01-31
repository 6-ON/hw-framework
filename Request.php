<?php

namespace sixon\hwFramework;

class Request
{
    public function getPath()
    {
        $path = $_SERVER["REQUEST_URI"];
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        } else {
            return substr($path, 0, $position);
        }
    }

    public function method(): string
    {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }


    public function isGet(): bool
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) === 'get';
    }

    public function isPost(): bool
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) === 'post';
    }

    public function isPut(): bool
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) === 'put';
    }
    public function isPatch(): bool
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) === 'patch';
    }

    public function isDelete(): bool
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) === 'delete';
    }


    public function getBody(): array
    {
        $body = [];

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = $value;
                    continue;
                }
                if ($value) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }else if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = $value;
                    continue;
                }
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }else {
            parse_str(file_get_contents("php://input"),$method_body);
            foreach ($method_body as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = $value;
                    continue;
                }
                $body[$key] = filter_var(INPUT_GET);
            }
        }

        return $body;
    }

    public function getFiles()
    {
        return $_FILES;
    }


    public function getNamesOfFiles()
    {
        $body = [];
        foreach ($_FILES as $key => $FILE) {
            $body[$key] = $FILE['name'];
        }
        return $body;
    }

}