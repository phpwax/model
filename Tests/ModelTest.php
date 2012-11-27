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
    $this->setExpectedException('Wax\Model\Exceptions\SchemaException');
    $model = new Example;
    $model->bad_field = "test";     
  }
  
  public function test_schema() {
    $model = new Example;
    $model->set_attributes($this->example_user);
    $keys = $model->_fieldset->accessible_keys();
    $this->assertEquals(count($keys), count($model->columns)-1);
    $this->assertEquals($model->_name, 'example');
  }
  
  public function test_observe_set() {
    $model = new Example;
    $observer = new MockObserver;
    $model->attach($observer);
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
    $this->assertInstanceOf('Wax\Model\Tests\MockBackend',$model->backend());    
  }
  
  public function test_default_backend() {
    $backend = new MockBackend;
    Example::default_backend($backend);
    $model = new Example;
    $this->assertInstanceOf('Wax\Model\Tests\MockBackend',$model->backend());    
    
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
  
  public function test_querying() {
    $model = new Example;
    $backend = new MockBackend;
    $model->set_backend($backend);
    
    $model->filter("username","test");
    $model->filter("username","again","!=");
    $model->order("testorder");
    $model->limit(5);
    $model->offset(2);
        
    $this->assertEquals(2, count($backend->query["filter"]));
    $this->assertEquals("testorder", $backend->query["order"]);
    $this->assertEquals(5, $backend->query["limit"]);
    $this->assertEquals(2, $backend->query["offset"]);
    
  }
  

}