<?php
namespace Wax\Model;
use Wax\Core\Event;
use Wax\Core\ObjectProxy;
use Wax\Model\Fields;
use Wax\Template\Helper\Inflections;
use Wax\Db\DbException;


/**
 * Base Database Class
 *
 * @package PHP-Wax
 * @author Ross Riley
 *
 * Allows models to be mapped to application objects
 **/
class Model implements SplSubject{

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
    $this->_observers = new SplObjectStorage();
    
    $this->set_write_key();
 		$this->setup();
    $this->load_fieldset();
 		$this->set_identifier();
    
    
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
  
  public set_write_key() {
    $class_name =  get_class($this);
 		if( $class_name != 'Model' && !$this->_table ) {
      $this->_table = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_name));
 		}
  }
 
  
  public function set_backend($backend) {
    $this->_backend = $backend;
  }
  
  public function attach(SplObserver $observer) {
    $this->_observers->attach($observer);
  }

  public function detach(SplObserver $observer) {
    $this->_observers->detach($observer);
  }
  
  public function observe($proxy) {
    if(!in_array($proxy, $this->_observers)) $this->_observers[] = $proxy;
  }
  
  public function notify($status = false) {
    if($status) $this->_status = $status; 
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
    $this->notify_observers("before_save",$this->row, $this->_fieldset);
  	if($this->_persistent) {  
  	  $res = $this->_backend->save($this->row, $this->_fieldset);
      $this->notify_observers("after_save",$this->row, $this->_fieldset);
  		return $res;
    }
    return $this;
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

  
  
  /********** Static Finder Methods ********/
  
  static public function find($finder, $params = array(), $scope_params = array()) {
    $class = get_called_class();
    if(is_numeric($finder)) return new $class($finder);
    if(is_array($params)) {
      $mod = new $class;
      foreach($params as $method=>$args) {
        $mod->$method($args);
      }
    } elseif(is_string($params)) {
      $mod = new $class($params);
      foreach($scope_params as $method=>$args) {
        $mod->$method($args);
      }
    }
    switch($finder) {
      case 'all':
        return $mod->all();
        break;
      case 'first':
        return $mod->first();
        break;
    }
  }
  
  static public function where($filters=[]) {
    $class = get_called_class();
    $mod = new $class;
    $mod->filter($filters);
    return $mod->all();
  }
  
  static public function create($attributes = []) {
 		$class = get_called_class();
    $new = new $class;
 		return $new->update_attributes($attributes);
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
      $this->notify_observers("before_get", $this, $name);
      $val = $this->row[$name];
      $this->notify_observers("after_get", $this, $name);
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
      $this->notify_observers("before_set", $this, $name);
      $this->row[$name]=$value;
      $this->notify_observers("after_set", $this, $name);
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
