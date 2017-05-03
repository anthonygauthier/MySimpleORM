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
         * @var [string] $Table
         * @var [object] $Object
         */
        protected $Database;
        protected $Table;
        protected $Object;

        /**
         * ObjectMapping::__construct()
         * Constructor & destructor
         * @param [string] $t
         * @param [object] $o
         */
        public function __construct($t, $o) {
            $className = get_class($o);

            $this->Database = new Database();
            $this->Table = $t;
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
                    "column" => "ID".$this->Table,
                    "value" => $id,
                    "condition" => "="
                )
            );
            
            if($this->Database->connect()) {
                $rows = array();
                $rows = $this->Database->select($this->Table, null, $where, $this->Database->getJoinsArray($this->Table));
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
            $className = get_class($this->Object);

            if($this->Database->connect()) {
                $rows = $this->Database->select($this->Table, null, $wheres, $this->Database->getJoinsArray($this->Table));
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
            $className = get_class($this->Object);

            foreach($objectAttributes as $attributeName=>$attributeValue) {
                if(($attributeName != "ID".$className) && ($attributeValue != null || $attributeValue != 0 || $attributeValue != "")) {
                    array_push($wheres, array(
                        "column" => $attributeName,
                        "value" => $attributeValue,
                        "condition" => "=",
                        "operation" => "AND"
                    ));
                }
            }

            if($this->Database->connect()) {
                $row = $this->Database->select($this->Table, null, $wheres, $this->Database->getJoinsArray($this->Table));
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

            $this->Database->insert($this->Table, $columns, $values);
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

            $className  = get_class($this->Object);
            $getterName = "getID" . $className;
            $objectID   = $this->Object->$getterName();
            $columns    = array();
            $values     = array();
            $where      = array(
                array(
                    "column" => "ID".$this->Table,
                    "value" => $objectID,
                    "condition" => "="
                )
            );

            $objectAttributes = $this->Object->getObjectAttributes($obj); 

            foreach($objectAttributes as $attributeName=>$attributeValue) {
                if($attributeName != "ID".$className) {
                    $getterName = "get" . $attributeName;

                    array_push($columns, $attributeName);
                    array_push($values , $this->Object->$getterName());
                }
            }

            $this->Database->update($this->Table, $columns, $values, $where);
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
                
            $className  = get_class($this->Object);
            $getterName = "getID" . $className;
            $objectID   = $obj->$getterName();
            $where      = array(
                array(
                    "column" => "ID".$this->Table,
                    "value" => $objectID,
                    "condition" => "="
                )
            );

            $this->Database->delete($this->Table, $where);
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
                $className = get_class($obj);
                $this->Object = new $className();

                foreach($attributes as $key=>$attribute) {
                    $setterName = "set".$key;

                    //If object contains other objects
                    if(strpos($key, "ID") !== false && $key != "ID".$className) {
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
