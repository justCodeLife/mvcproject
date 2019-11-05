<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $params = [];
    protected $namespace = 'App\Controllers\\';

    public function add($route, $params)
    {
        $route = preg_replace('/^\//', '', $route);
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?<\1>[a-z0-9-]+)', $route);
        $route = '/^' . $route . '\/?$/i';

        if (is_string($params)) {
            list($allParams['controller'], $allParams['method']) = explode('@', $params);
        }

        if (is_array($params)) {
            list($allParams['controller'], $allParams['method']) = explode('@', $params['uses']);
            unset($params['uses']);
            $allParams = array_merge($allParams, $params);
        }

        $this->routes[$route] = $allParams;
    }

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params['params'][$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function dispatch($url)
    {
        $url = $this->removeVariablesOfQueryString($url);
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->getNamespace() . $controller;
            if (class_exists($controller)) {
                $controller_object = new $controller();
                $method = $this->params
                ['method'];
                if (is_callable([$controller_object, $method])) {
                    $this->params['params'] = isset($this->params['params']) ? $this->params['params'] : [];
                    echo call_user_func_array([$controller_object, $method], $this->params['params']);
                } else {
                    throw new \Exception("Method {$method} in controller {$controller} not found");
                }
            } else {
                throw new \Exception("Controller {$controller} not found!");
            }

        } else {
            throw new \Exception("No route matched", 404);
        }
    }

    public function getNamespace()
    {
        $namespace = $this->namespace;
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

    protected function removeVariablesOfQueryString($url)
    {
        if ($url != '') {
            $parts = explode("&", $url, 2);
            if (strpos($parts[0], '=') == false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
            return $url;
        }
    }
}