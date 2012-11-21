<?php
namespace Wax\Model\Fields;
use Wax\Model\Field;

/**
 * GuidField class
 *
 * @package PHP-Wax
 **/
class GuidField extends Field {
  
  public $null = false;
  public $default = false;
  public $maxlength = "64";
  public $auto = false;
  public $primary = true;
  public $editable = false;


} 
