<?php
namespace Wax\Model\Tests;


/**
 *
 * @package PHP-Wax
 **/
class MockField implements \SplObserver {
  
  public $field;
  public $value;
  public $editable;
  public $observer_calls = [];
  
    
  public function __construct($column, $options = array()) {
    foreach($options as $option=>$val) $this->$option = $val;
    if(!$this->field) $this->field = $column;
    $this->setup();
  }
  
  public function setup() {
    
  }
  
  public function save() {
    
  }
  
  
  // This represents all the listeners a Field object should listen for
  public function update(\SplSubject $object) {
    if($object->_event_data !== $this->field) return;
    $this->observer_calls[] = $object->_status;
    if(is_callable(array($this, $object->_status))) {
      call_user_func(array($this, $object->_status),$object, $object->_event_data);
    }   
  }
  
 
  
  public function before_save($object) {}
  public function after_save($object) {}

  public function before_load($object) {}
  public function after_load($object) {}

  
  public function before_set($object, $field) {}
  public function after_set($object, $field) {
    $object->row[$field] = "intercepted";
  }
  
  public function before_get($object, $field) {}
  public function after_get($object, $field) {}
  


}