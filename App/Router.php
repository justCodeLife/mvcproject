<?php

namespace App;

use Core\Router;

$router = new Router();

$router->add('/','HomeController@index');

return $router;
