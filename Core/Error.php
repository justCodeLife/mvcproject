<?php

namespace Core;

class Error
{
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
        if (env('DEBUG_MODE',false)) {
            echo "<h1>Fatal Error !</h1>";
            echo "<br>";
            echo "Uncaught Exception :( <p>' " . get_class($exception) . " '</p>";
            echo "Message : ' " . $exception->getMessage() . " '</p>";
            echo "Stack trace : <p><pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "Thrown in <p>'" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__) . '/Storage/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "\n\nFatal Error !\n\n";
            $message .= "Uncaught Exception : " . get_class($exception) . "\n\n";
            $message .= "Message : '" . $exception->getMessage() . "'\n\n";
            $message .= "Stack trace : " . $exception->getTraceAsString() . "\n\n";
            $message .= "Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "\n\n";

            error_log($message);

            echo View::renderTemplate("errors.{$code}");

        }
    }
}