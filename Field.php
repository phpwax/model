<?php
namespace Wax\Model;


/**
 * WaxModelFields class
 *
 * @package PHP-Wax
 **/
class Field implements \SplObserver {
    
  // Database Specific Configuration
  public $field       = false;          // How this column is referred to
  public $null        = true;           // Can column be null
  public $default     = false;       //default value for the column  
  public $primary_key = false;  // the primay key field name - der'h
  public $table       = false;          // Table name in the storage engine
  public $col_name    = false;               // Actual name in the storage engine
  
  //Validation & Format Options
  public $maxlength     = false; 
  public $minlength     = false;
  public $choices       = false; //for select fields this is an array
  public $text_choices  = false; // Store choices as text in database
  public $editable      = true; // Only editable options will be displayed in forms
  public $blank         = true; 
  public $required      = false; 
  public $unique        = false;
  public $show_label    = true;
  public $label         = false;
  public $help_text     = false;
  public $widget        = "TextInput";
  public $data_type     = "string";
  
  

  public function __construct($column, $options = array()) {
    foreach($options as $option=>$val) $this->$option = $val;
    if(!$this->field) $this->field = $column;
    $this->setup();
  }
  
  public function setup() {
    if(!$this->col_name) $this->col_name = $this->field;
  }
  
  
  public function update(\SplSubject $object) {
    if($object->_event_data !== $this->field) return;
    if(is_callable(array($this, $object->_status))) {
      call_user_func(array($this, $object->_status),$object, $object->_event_data);
    }   
  }
  
}