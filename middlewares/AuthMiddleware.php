<?php

namespace sixon\hwFramework\middlewares;

use sixon\hwFramework\Application;
use sixon\hwFramework\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];
    public array $loggedactions = [];

    public function __construct(array $actions = [],array $loggedactions =[])
    {
        $this->actions = $actions;
        $this->loggedactions = $loggedactions;
    }

    public function execute()
    {
        if (Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->action,$this->actions)){
                throw new ForbiddenException();
            }
        }else if(in_array(Application::$app->controller->action,$this->loggedactions)) {
                Application::$app->response->redirect('/');
            }
        
    }
}