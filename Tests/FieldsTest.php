<?php
namespace Wax\Model\Tests;


class FieldsTest extends \PHPUnit_Framework_TestCase {

  public function setup() {
    $this->example_user = ["username"=>"test1", "password"=>"password", "email"=>"test1@test.com"];
  }
  
  public function teardown() {
    
  }
  
  /**
   * 
   *
   **/
  
  public function test_observers() {
    $model = new Example;
    $backend = new MockBackend;
    $model->set_backend($backend);
    $model->set_attributes($this->example_user);
    $this->assertEquals($model->fieldset()->key, 'example');
    
    
    $model->mock = "now_set";
    $this->assertContains("before_set", $model->fieldset()->mock->observer_calls );
    $this->assertContains("after_set", $model->fieldset()->mock->observer_calls);
    $this->assertEquals($model->mock, 'intercepted');
    $this->assertContains("before_get", $model->fieldset()->mock->observer_calls );
    $this->assertContains("after_get", $model->fieldset()->mock->observer_calls);
    
    
    $model->save();
    $this->assertContains("before_save", $model->fieldset()->mock->observer_calls );
    $this->assertContains("after_save", $model->fieldset()->mock->observer_calls );
    
  }
  
  
}
  
