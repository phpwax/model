<?php
namespace Wax\Model\Fields;
use Wax\Model\Field;


/**
 * EmailField class
 *
 * @package PHP-Wax
 **/
class DateTimeField extends Field {
  
  public $null = true;
  public $default = false;
  public $maxlength = false;
  public $widget = "DateInput";
  public $output_format = "Y-m-d H:i:s";
  public $save_format = "Y-m-d H:i:s";
  public $use_uk_date = false;
  public $data_type = "date_and_time";
  
  

  

} 
