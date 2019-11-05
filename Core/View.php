<?php

namespace Core;

use Jenssegers\Blade\Blade;

class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);
        $file = "../App/Views/{$view}.php";

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("File {$file} not found !");
        }

    }

    public static function renderTemplate($template, $args = [])
    {
        $views = realpath(__DIR__ . '/../App/Views');
        $cache = realpath(__DIR__ . '/../Storage/views');
        $blade = new Blade($views, $cache);
        return $blade->make($template, $args)->render();
    }
}