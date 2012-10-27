<?php
namespace Wax\Model\Tests;


class MockObserver  {
  
  public $events = [];
  
  public function notify($label, $object) {
    $this->events[$label]=$object;
  }
  
}