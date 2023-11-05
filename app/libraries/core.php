<?php

namespace App\Libraries;

class Core
{
    protected $currentController = 'users';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();
        if (isset($url) && file_exists(APP_ROOT . '/controllers/' . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }
        require_once(APP_ROOT . '/controllers/' . $this->currentController . '.php');
        $this->currentController = new('App\\Controllers\\' . $this->currentController);

        if (isset($url[1]) && method_exists($this->currentController, $url[1])) {

            $this->currentMethod = $url[1];
            unset($url[1]);

        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($url) {
            $params = array_values($url);
            $this->params = array_unshift($params, $requestMethod);
        } else {
            $this->params = [$requestMethod];
        }

        call_user_func_array([
            $this->currentController,
            $this->currentMethod
        ], $this->params);
    }

    /**
     * Collects the data sent in the url.
     * @return array|void
     */

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}