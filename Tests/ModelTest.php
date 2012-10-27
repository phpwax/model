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

class ModelTest extends \PHPUnit_Framework_TestCase {

  public function setup() {
    
  }
  
  public function teardown() {
    
  }


  public function test_create() {
    $model = new Example;
    $model->set_attributes(["username"=>"test1", "password"=>"password", "email"=>"test1@test.com"]);
    $this->assertInstanceOf('Wax\Model\Tests\Example',$model);
    $this->assertEquals($model->username, "test1");
  }
    
  public function test_all() {
    // $res = $this->model->all();
    //     $this->assertInstanceOf("WaxRecordset",$res);      
  }
    
  public function test_first() {
    // $res = $this->model->create($this->get_fixture("user1"));
    //     $res = $this->model->first();
    //     $this->assertInstanceOf("WaxModel",$res);
    //     $this->assertEquals($res->username, "test1");
  }
    
  public function test_delete() {
    // $res = $this->model->create($this->get_fixture("user1"));
    //     $res = $this->model->filter(array("username"=>"test1"))->all()->delete();
    //     $res = $this->model->filter(array("username"=>"test1"))->first();
    //     $this->assertFalse($res);
  }
    
  public function test_multiple_delete() {
    // $this->model->create($this->get_fixture("user1"));
    // $this->model->create($this->get_fixture("user2"));
    // $this->assertEquals($this->model->all()->count(), "2");
    // $this->model->filter("username","test1")->all()->delete();
    // $this->assertEquals($this->model->clear()->all()->count(), "1");
    // $this->model->clear()->query("delete from ".$this->model->table);
    // $this->assertEquals($this->model->clear()->all()->count(), "0");
  }
    
  public function test_update() {
    // $res = $this->model->create($this->get_fixture("user1"));
    // $res = $this->model->filter(array("username"=>"test1"))->all();
    // $this->assertEquals($res->count(), "1");
    // $this->model->clear()->query("delete from ".$this->model->table);
  }
    
  public function test_multiple_filters() {
    // $res = $this->model->create($this->get_fixture("user2"))->update_attributes(array("username"=>"altered"));
    // $this->model->create($this->get_fixture("user3"));
    // $res = $this->model->filter(array("password"=>"password"))->all()->filter("username !='altered'")->all();
    // $this->assertEquals($res->count(), 1);
    // $this->model->clear()->query("delete from ".$this->model->table);
  }
    
  public function test_filter_security() {
    // $this->model->create(array("username"=>"d'oh", "password"=>"password", "email"=>"test@example.com"));
    // $res = $this->model->filter(array("username"=>"d'oh"))->first();
    // $this->assertEquals($res->username, "d'oh");
    // $res2 = $this->model->filter(array("username = ? AND password=?"=>array("d'oh", "password")))->first();
    // $this->assertEquals($res2->username, "d'oh");
    // $res3 = $this->model->filter(array("username"=>array("d'oh")))->first();
    // $this->assertEquals($res3->username, "d'oh");
  }
		
  public function test_equal(){

  }

}