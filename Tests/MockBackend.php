<?php
namespace Wax\Model\Tests;

/**
 * Mock to implement only the essential interface of a backend
 * Methods supported: all, first, find, save
 *
 */


class MockBackend  {
  
  public $query = [
    "filter"         => [],
    "order"          => false,
    "limit"          => false,
    "offset"         => "0",
    "raw"            => false,
  	"select"         => []
  ];
  
  public $operations = [];
  
  
  public function __construct($options = []) {
    if(isset($options['schema'])) $this->_schema = $options['schema'];
  }
  
  public function save($options) {
    $this->operations["save"][]=$options;
    if(isset($options['data'])) return new \ArrayObject($options['data'], \ArrayObject::ARRAY_AS_PROPS);
    return false;
  }
  
  public function all($query=[]) {
    $this->operations["all"][]=$this->query;
  }
  
  public function first($query=[]) {
    $this->operations["first"][]=$this->query;
  }
  
  
  public function find($key) {
    $this->operations["find"][]=$key;
  }
  
  public function __call($method, $params) {
    if(isset($this->query[$method])) {
      if(is_array($this->query[$method])) $this->query[$method][]=$params;
      elseif(count($params)==1) $this->query[$method]=$params[0];
      else $this->query[$method]=$params;
      return $this;
    } else {
      throw new BackendSupportException;
    }
  }
  

  
}