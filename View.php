<?php

namespace sixon\hwFramework;

class View
{
    public string $title = '';
    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderViewOnly($view, $params);
        $layoutContent = $this->renderLayout();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }


    public function renderViewOnly($view, $params = [])
    {
        extract($params);
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }


    private function renderLayout()
    {
        $layout = 'main';
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }



}