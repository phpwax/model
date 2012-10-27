<?php
namespace Wax\Model;
use Wax\Core\Exception;
use Wax\Core\ObjectProxy;

/**
 * Takes care of mapping model values to fields.
 *
 * @package PHP-WAX
 */
class Fieldset  {
  
  public $keys           = [];    
  public $associations   = [];    
  public $columns        = [];
  public $parent_model   = FALSE;
  
  public function __construct($parent_model) {
    $this->parent_model = $parent_model;
  }
  
  
  public function add($column, $type, $options=[]) {
    (isset($options["target_model"])) ? $this->set_association($column) : $this->set_key($column);    
    if(!class_exists($type)) $class = "Wax\\Model\\Fields\\".$type;
    $this->columns[$column] = new $class($column, $options);
    $this->parent_model->observe($this->columns[$column]);
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
  
  
  
}
