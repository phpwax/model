<?php
namespace Wax\Model\Tests;


class MockBackend  {
  
  public $query = [
    "filter"         => [],
    "order"          => false,
    "limit"          => false,
    "offset"         => "0",
    "raw"            => false,
  	"select"         => []
  ];
  
  public $_data      = false;
  public $_schema    = false;
  
  public function __construct($options = []) {
    if(isset($options['schema'])) $this->_schema = $options['schema'];
  }
  
  public function save($data, $schema) {
    $this->_data   = $data;
    $this->_schema = $schema;
    return new \ArrayObject($data, \ArrayObject::ARRAY_AS_PROPS);
  }
  
  public function insert($data) {
    
  }
  
  public function update($data) {

  }
  
  
  public function fetch($query = false) {
    
  }
  
  public function __call($method, $params) {
    if(isset($this->_query['method'])) {
      $this->_query['method']=$params;
    }
    return $this;
  }

  
}