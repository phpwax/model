<?php
namespace Wax\Model\Tests;


class MockObserver implements \SplObserver  {
  
  public $events = [];
  
  public function update(\SplSubject $object) {
    $this->events[$object->_status]=$object;
  }
  
}