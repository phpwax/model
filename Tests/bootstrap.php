<?php
spl_autoload_register(function ($class) {
  if (0 === strpos(ltrim($class, '/'), 'Wax\Model')) {
    if (file_exists($file = __DIR__.'/../'.substr(str_replace('\\', '/', $class), strlen('Wax\Model')).'.php')) {
      require_once $file;
    }
  }
});