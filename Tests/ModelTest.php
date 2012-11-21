<?php
namespace Wax\Model\Tests;


class ModelTest extends \PHPUnit_Framework_TestCase {

  public function setup() {
    $this->example_user = ["username"=>"test1", "password"=>"password", "email"=>"test1@test.com"];
  }
  
  public function teardown() {
    
  }


  public function test_create() {
    $model = new Example;
    $model->set_attributes($this->example_user);
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
    $model->set_attributes($this->example_user);
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
  
	
  public function test_backend(){
    $model = new Example;
    $backend = new MockBackend;
    $model->set_backend($backend);
    $this->assertInstanceOf('Wax\Model\Tests\MockBackend',$model->_backend);    
  }
  
  public function test_save() {
    $model = new Example;
    $backend = new MockBackend;
    $model->set_backend($backend);
    $model->set_attributes($this->example_user);
    $result = $model->save();
    $this->assertEquals($result->username, "test1");
    $this->assertEquals($result->password, "password");
    $this->assertEquals($result->email, "test1@test.com");
  }

}