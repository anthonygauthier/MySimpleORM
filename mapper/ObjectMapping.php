<?php
/*
* Author      : Anthony Gauthier
* Owner       : Anthony Gauthier
* Date created  : 2017-03-24
* Date modified : 2017-04-04
* Software    : YOUR PROJECT
* File        : ObjectMapping.php
* Description : Object Mapping class, serves as a database abstraction layer
*/
    require_once("ObjectMappingInterface.php");
    require_once("Database.php");

    class ObjectMapping implements ObjectMappingInterface {
        /**
         * Variables
         * 
         * @var [Database] $Database
         * @var [object] $Object
         */
        protected $Database;
        protected $Object;
        protected $ClassName;

        /**
         * ObjectMapping::__construct()
         * Constructor & destructor
         * @param [string] $t
         * @param [object] $o
         */
        public function __construct($o) {
            $this->ClassName = get_class($o);
            $this->Database = new Database();
            $this->Object = $o;
        }

        public function __destruct() {}

        /**
         * Retrieves a record in the database by ID
         * 
         * @param [int] $id
         * @return $this->Object
         */
        public function findById($id) {
            $where = array(
                array(
                    "column" => "ID".$this->ClassName,
                    "value" => $id,
                    "condition" => "="
                )
            );
            
            if($this->Database->connect()) {
                $rows = array();
                $rows = $this->Database->select($this->ClassName, null, $where, $this->Database->getJoinsArray($this->ClassName));
                $this->mapObject($this->Object, $rows);

                return $this->Object;
            }
        }

        /**
         * getObjectArray
         * 
         * @return void
         */
        public function getObjectArray($wheres=null) {
            $return = array();
            $rows = array();

            if($this->Database->connect()) {
                $rows = $this->Database->select($this->ClassName, null, $wheres, $this->Database->getJoinsArray($this->ClassName));
                $this->mapObject($this->Object, $rows, $return);
            }

            return $return;
        }

        /**
         * findByParam
         * 
         * @param [type] $array
         * @return void
         */
        public function findByParam($array) {
            //TODO. Will return array of objects ?
        }

        /**
         * getCurrentObject
         * 
         * @param [type] $obj
         * @return void
         */
        public function getCurrentObject($obj=null) {
            if($obj != null)
                $this->Object = $obj;
                
            $objectAttributes = $this->Object->getObjectAttributes($obj);
            $wheres = array(); 

            foreach($objectAttributes as $attributeName=>$attributeValue) {
                if(($attributeName != "ID".$this->ClassName) && ($attributeValue != null || $attributeValue != 0 || $attributeValue != "")) {
                    array_push($wheres, array(
                        "column" => $attributeName,
                        "value" => $attributeValue,
                        "condition" => "=",
                        "operation" => "AND"
                    ));
                }
            }

            if($this->Database->connect()) {
                $row = $this->Database->select($this->ClassName, null, $wheres, $this->Database->getJoinsArray($this->ClassName));
                $this->mapObject($this->Object, $row);
            }

            return $this->Object;
        }

        /**
         * insertObject
         * 
         * @param [type] $obj
         * @return void
         */
        public function insertObject($obj=null) {
            if($obj != null)
                $this->Object = $obj;

            $objectAttributes = $this->Object->getObjectAttributes($obj); 
            $columns          = array();
            $values           = array();

            foreach($objectAttributes as $attributeName=>$attributeValue) {
                $getterName = "get" . $attributeName;

                array_push($columns, $attributeName);
                array_push($values , $this->Object->$getterName());
            }

            $this->Database->insert($this->ClassName, $columns, $values);
        }

        /**
         * updateObject
         * 
         * @param [type] $obj
         * @return void
         */
        public function updateObject($obj=null) {
            if($obj != null)
                $this->Object = $obj;
            
            $getterName = "getID" . $this->ClassName();
            $objectID   = $this->Object->$getterName();
            $columns    = array();
            $values     = array();
            $where      = array(
                array(
                    "column" => "ID".$this->ClassName,
                    "value" => $objectID,
                    "condition" => "="
                )
            );

            $objectAttributes = $this->Object->getObjectAttributes($obj); 

            foreach($objectAttributes as $attributeName=>$attributeValue) {
                if($attributeName != "ID".$this->ClassName) {
                    $getterName = "get" . $attributeName;

                    array_push($columns, $attributeName);
                    array_push($values , $this->Object->$getterName());
                }
            }

            $this->Database->update($this->ClassName, $columns, $values, $where);
        }

        /**
         * deleteObject
         * 
         * @param [type] $obj
         * @return void
         */
        public function deleteObject($obj=null) {
            if($obj != null)
                $this->Object = $obj;
                
            $getterName = "getID" . $this->ClassName();
            $objectID   = $obj->$getterName();
            $where      = array(
                array(
                    "column" => "ID".$this->ClassName,
                    "value" => $objectID,
                    "condition" => "="
                )
            );

            $this->Database->delete($this->ClassName, $where);
            $this->__destruct();
        }
        /**
         * Undocumented function
         * 
         * @param [type] $obj - referenced object
         * @param [type] $rows - rows retrived by DB
         * @param [type] $objArr - referenced object array
         * @return void
         */
        public function mapObject(&$obj, $rows, &$objArr=null) {
            $attributes = $obj->getObjectAttributes($obj);
            
            foreach($rows as $row) {
                $this->Object = new $this->ClassName();

                foreach($attributes as $key=>$attribute) {
                    $setterName = "set".$key;

                    //If object contains other objects
                    if(strpos($key, "ID") !== false && $key != "ID".$this->ClassName) {
                        $linkedClassName = str_replace("ID", "", $key);
                        $objectToPush = new $linkedClassName();
                        $objectToPush = $objectToPush->findObjectById($row[$key]);
                        $obj->$setterName($objectToPush);
                    } else {
                        $obj->$setterName($row[$key]);
                    }
                }

                //add to array if array isset - getObjectArray()
                if(isset($objArr)) {
                    $objArr[] = $obj;
                }
            }
        }
    }
?>
