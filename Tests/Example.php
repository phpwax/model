<?php
namespace Wax\Model\Tests;
use Wax\Model\Model;


class Example extends Model {
  public function setup() {
    $this->define("username", "CharField", array("maxlength"=>40));
    $this->define("password", "PasswordField", array("blank"=>false, "maxlength"=>15));
    $this->define("email", "EmailField", array("blank"=>false));
  }
}