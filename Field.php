<?php
namespace Wax\Model;


/**
 * WaxModelFields class
 *
 * @package PHP-Wax
 **/
class Field implements \SplObserver {
    
  // Database Specific Configuration
  public $field       = FALSE;          // How this column is referred to
  public $null        = TRUE;           // Can column be null
  public $default     = FALSE;       //default value for the column  
  public $primary_key = FALSE;  // the primay key field name - der'h
  public $table       = FALSE;          // Table name in the storage engine
  public $col_name    = FALSE;               // Actual name in the storage engine
  
  //Validation & Format Options
  public $maxlength     = FALSE; 
  public $minlength     = FALSE;
  public $choices       = FALSE; //for select fields this is an array
  public $text_choices  = FALSE; // Store choices as text in database
  public $editable      = TRUE; // Only editable options will be displayed in forms
  public $blank         = TRUE; 
  public $required      = FALSE; 
  public $unique        = FALSE;
  public $show_label    = TRUE;
  public $label         = FALSE;
  public $help_text     = FALSE;
  public $widget        ="TextInput";
  public $data_type     = "string";
  

  public function __construct($column, $options = array()) {
    foreach($options as $option=>$val) $this->$option = $val;
    if(!$this->field) $this->field = $column;
    $this->setup();
  }
  
  public function setup() {
    if(!$this->col_name) $this->col_name = $this->field;
  }
  
  public function update($object, $field=FALSE) {
    if($object->_status == "before_save")   $this->before_save($object);
    if($object->_status == "after_save")    $this->after_save($object);
    
    if($object->_status == "before_set" && $object->_event_data == $this->field)    $this->before_set($object, $field);
    if($object->_status == "after_set"  && $object->_event_data == $this->field)    $this->after_set($object, $field);
    if($object->_status == "before_get" && $object->_event_data == $this->field)    $this->before_get($object, $field);
    if($object->_status == "after_get"  && $object->_event_data == $this->field)    $this->after_get($object, $field);    
  }
  
  public function before_save($object) {}
  public function after_save($object) {}
  
  public function before_set($object, $field) {}
  public function after_set($object, $field) {}
  
  public function before_get($object, $field) {}
  public function after_get($object, $field) {}
  


}