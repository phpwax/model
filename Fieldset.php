<?php
namespace Wax\Model;

/**
 * Takes care of mapping model values to fields.
 *
 * @package PHP-WAX
 */
class Fieldset implements \SplObserver  {
  
  public $key            = false;
  public $keys           = [];    
  public $associations   = [];    
  public $columns        = [];
  public $parent_model   = false;
  public $unique_key     = false;
  
  public function __construct($settings = []) {
    foreach($settings as $k=>$v) $this->$k = $v;
  }
  
  
  public function add($column, $type, $options=[]) {
    (isset($options["target_model"])) ? $this->set_association($column) : $this->set_key($column);    
    if(!class_exists($type)) $class = "Wax\\Model\\Fields\\".$type;
    else $class = $type;
    
    if(class_exists($class)) $this->columns[$column] = new $class($column, $options);
  }
  
  public function columns() {
    return $this->columns;
  }
  
  public function keys() {
    return $this->keys;
  }
  
  public function accessible_keys() {
    $keys = [];
    foreach($this->columns() as $key=>$field) {
      if($field->editable) $keys[]=$key;
    }
    return $keys;
  }
  
  public function set_key($key) {
    if(!in_array($key,$this->keys)) $this->keys[] = $key;
  }
  
  public function set_association($key) {
    if(!in_array($key,$this->associations)) $this->associations[] = $key;
  }
  
  public function associations() {
    return $this->associations;
  }
  
  /**
   * Which field to use as a human identifiable label
   *
   * @return string
   **/
  public function identifier() {
    foreach($this->columns() as $name=>$col) {
      if($col->data_type=="string") return $name;
    }
  }
  
  public function update(\SplSubject $object) {
    foreach($this->columns as $k=>$v) {
      $v->update($object);
    }
  }
  
  public function __get($name) {
    if(is_array($this->columns) && array_key_exists($name, $this->columns)) return $this->columns[$name];
  }
  
  
  
}
