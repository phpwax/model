<?php
namespace Wax\Model\Tests;
use Wax\Model\Tests\ExampleModel;


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
    $this->assertEquals($model->password, "password");
    $this->assertEquals($model->email, "test1@test.com");
  }
  
  public function test_bad_schema_write() {
    $this->setExpectedException('Wax\Model\SchemaException');
    $model = new Example;
    $model->bad_field = "test";     
  }
  
  public function test_schema() {
    $model = new Example;
    $model->set_attributes(["username"=>"test1", "password"=>"password", "email"=>"test1@test.com"]);
    $keys = $model->_fieldset->accessible_keys();
    $this->assertEquals(count($keys), 3);    
  }
  
  public function test_observe_set() {
    $model = new Example;
    $observer = new MockObserver;
    $model->observe($observer);
    $model->username = "test";
    $this->assertInstanceOf('Wax\Model\Tests\Example',$observer->events["before_set"]);
    $this->assertInstanceOf('Wax\Model\Tests\Example',$observer->events["after_set"]);
    $this->assertEquals($observer->events["before_set"]->username,'test');
    $this->assertEquals($observer->events["after_set"]->username,'test');
  }
  
  

		
  public function test_equal(){

  }

}