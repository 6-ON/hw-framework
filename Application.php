<?php

namespace app\core;

use app\core\db\Database;
use app\core\db\DbModel;

class Application
{
    public string $userClass;
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public static string $ROOT_DIR;
    public ?Controller $controller = null;
    public ?UserModel $user;

    public Database $db;
    public View $view;

    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }

        $this->view = new View();
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $exception) {
            $code = is_int($exception->getCode()) ? $exception->getCode() : 500;
            self::$app->response->setStatusCode($code);
            self::$app->controller= new Controller();
            self::$app->controller->layout='empty';
            echo self::$app->view->renderView('error',
                ['exception' => $exception
            ]);
        }
    }

    public function login(DbModel $user)
    {
        $this->user = $user;
        $pK = $user::primaryKey();
        $pV = $user->{$pK};
        $this->session->set('user', $pV);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }


    public static function isGuest()
    {
        return !self::$app->user;
    }
}
