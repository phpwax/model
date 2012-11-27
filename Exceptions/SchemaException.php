<?php
namespace Wax\Model\Exceptions;
/**
 *
 * @package PHP-Wax
 * @author Ross Riley
 **/
class SchemaException extends \Exception {
  
  public $message = "<p>You tried to write to a model in a way the defined schema does not support:</p>";
  
	function __construct($model,$write_name, $previous = NULL) {
    $message = $this->message;
    $message .= "<pre>Writing to:".$write_name."<br>The Following are available:</pre>";
	  $message .= "<pre>".print_r($model->_fieldset->keys(), 1)."</pre>";
	  $message .= "<p>Check out the definitions in the ".get_class($model)." class setup and try again.</p>";
  	parent::__construct( $message);
  }
}



