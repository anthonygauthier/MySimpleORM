<?php
namespace MySimpleORM\Utils;

class Transform
{

  static function split_conditions($wheres) 
  {
    $transformedArray = array();

    if (is_array($wheres) && $wheres != null) {
      if (!array_key_exists("condition", $wheres)) {
        foreach($wheres as $key => $value) {
          $splitValues = explode(',', $value);
          array_push($transformedArray, array(
            "column" => $splitValues[0],
            "condition" => $splitValues[1],
            "value" => $splitValues[2]
          ));
        }
        return $transformedArray;
      }
    }
  }

}
?>