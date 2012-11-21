<?php
namespace Wax\Model\Tests;
use Wax\Model\Model;


class Example extends Model {
  
  public $columns = [
    'username'  =>['CharField',     ['maxlenth'=>40]],
    'password'  =>['PasswordField', ['blank'=>false,'maxlenth'=>15]],
    'email'     =>['EmailField',    ['blank'=>false]]
  ];

  

}