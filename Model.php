<?php
namespace Wax\Model;
use Wax\Model\Fields;


/**
 * Base Database Class
 *
 * @package PHP-Wax
 * @author Ross Riley
 *
 * Allows models to be mapped to application objects
 **/
class Model implements \SplSubject{

  static public $db_settings  = false;
  static public $db           = false;
  public $_table               = false;
  public $row                 = [];
  public $_unique_key         = "id";
  public $_primary_type       = "GuidField";
  public $_primary_options    = [];
  public $_identifier         = false;

  public $_readable           = true;  // Set to false to disallow reading from the backend.
  public $_is_paginated       = false;
  public $_tainted            = false; // set to true when a write operation has been performed.
  public $_create             = false; // Indicates that a creation is required


  public $_backend            = false;
  public $_fieldset           = false;
  
  public $_observers          = false; // SplStorage stack that manages attached observers
  public $_status             = false; // Internal status that allows observers to read
  public $_event_data         = false; // 
  
  public $_response           = false; // Stores object response, prior to returning
  
  
  static public $_default_backend    = false; 

  /**
   *  constructor
   *  @param  mixed   param   PDO instance,
   *                          or record id (if integer),
   *                          or constraints (if array) but param['pdo'] is PDO instance
   */
 	function __construct($params=null) {
    if(isset($params["backend"])) $this->set_backend($params["backend"]);
    $this->_observers = new \SplObjectStorage();
    
    $this->set_write_key();
    $this->load_fieldset();
 		$this->set_identifier();
 		$this->setup();
    
    
 		// Handles initialisers passed into the constructor run a method called scope_[scope]() or if an `id` then load that model.
 		if($params) {
 		  $method = "scope_".$params;
	    if(method_exists($this, $method)) {$this->$method;}
	    else {
        $this->notify("before_load");
	      $res = $this->filter([$this->primary_key=>$params])->first();
   		  $this->row=$res->row;
   		  $this->clear();
        $this->notify("after_load");
	    }
	  }
 	}
  
  public function set_write_key() {
    $class_name =  get_class($this);
 		if( $class_name != 'Model' && !$this->_table ) {
      $this->_table = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_name));
 		}
  }
 
  
  public function set_backend($backend) {
    $this->_backend = $backend;
  }
  
  public function attach(\SplObserver $observer) {
    $this->_observers->attach($observer);
  }

  public function detach(\SplObserver $observer) {
    $this->_observers->detach($observer);
  }
  
  
  public function notify($status = false, $data = false) {
    if($status) $this->_status = $status; 
    if($data)   $this->_event_data = $data; 
    foreach ($this->_observers as $observer) $observer->update($this);
  }
  
  
  
  public function load_fieldset() {
    if(!$this->_fieldset) {
      $this->_fieldset = new Fieldset($this);
      foreach($this->columns as $col=>$details) {
        $this->define($col, array_shift($details), $details);
      }
    }
  }

 	public function define($column, $type, $options=array()) {
    $this->_fieldset->add($column,$type, $options);
 	}
  
  
  public function columns() {
    return $this->_fieldset->columns();
  }
  
  public function writable_columns() {
    return array_intersect_key($this->row, array_fill_keys($this->_fieldset->keys(),1 ));
  }




	/************** Methods that hit the database ****************/
  
  /**
   *  Insert record to table, or update record data
   */
  public function save() {
    $this->notify("before_save");
  	$this->_response = $this->_backend->save($this->row, $this->_fieldset);
    $this->notify("after_save");
  	return $this->_response;
  }

  public function query($query) {
    return self::$_backend->query($query);
  }

  

 	public function all() {
    $fetch = self::$_backend->exec();
    foreach($fetch as $row) {
      $model = clone $this;
      $model->row = $row;
      $records[]=$model;
    }
 	  return new Recordset($this, $records);
 	}

 	public function rows() {
 	  return self::$_backend->select($this);
 	}

 	public function first() {
 	  $this->_query->limit(1);
 	  $model = clone $this;
 	  $res = self::$_backend->exec();
 	  if($res[0]) $model->row = $res[0];
 	  else $model = false;
 	  return $model;
 	}


 	public function update_attributes($array) {
 	  $this->set_attributes($array);
		return $this->save();
 	}
  


 	/************ End of database methods *************/


 	public function set_attributes($array) {
		foreach($array as $k=>$v) $this->$k=$v;
	  return $this;
	}

  


  /********** Magic Methods **************/
  
  
  /**
   * By default, any method that can't be handled by the model,
   * Will be passed along to the backend, as long as the backend class
   * is able to handle the method.
   *
   * @return mixed
   **/
  public function __call($method, $args) {
    if($this->_backend) return call_user_func_array(array($this->_backend, $method), $args);
  }
  

  public function __clone() {
  	$this->setup();
   }
   
   /**
    *  get property
    *  @param  string  name    property name
    *  @return mixed           property value
    */
  public function __get($name) {
    if(in_array($name, $this->_fieldset->keys)|| in_array($name, $this->_fieldset->associations())) {
      $this->notify("before_get", $name);
      $val = $this->row[$name];
      $this->notify("after_get", $name);
      return $val;
    }
    elseif(method_exists($this, $name)) return $this->{$name}();
    elseif(is_array($this->row) && array_key_exists($name, $this->row)) return $this->row[$name];
  }


  /**
   *  set property
   *  @param  string  name    property name
   *  @param  mixed   value   property value
   */
  public function __set( $name, $value ) {
    if(in_array($name, $this->_fieldset->keys())|| in_array($name, $this->_fieldset->associations())) {
      $this->notify("before_set", $name);
      $this->row[$name]=$value;
      $this->notify("after_set", $name);
    } else throw new SchemaException($this, $name);
  }

  /**
   *  __toString overload
   *  @return  primary key of class
   */
  public function __toString() {
    return $this->{$this->primary_key};
  }



}
