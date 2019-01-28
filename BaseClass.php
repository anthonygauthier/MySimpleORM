<?php
    /*
    * Author      : Anthony Gautthier
    * Owner       : Anthony Gauthtier
    * Date created  : 2017-04-04
    * Date modified : 2017-05-02
    * Software    : YOUR_PROJECT
    * File        : BaseClass.php
    * Description : Class that detains all the basic find/delete/insert/update for any given object
    */
    require_once("models/mapper/ObjectMapping.php");

    class BaseClass {
        protected $Mapper;

        public function __construct($className, $object) {
            $this->Mapper = new ObjectMapping($className, $object);
        }

        public function __destruct()  {}

        function toJSON(){
            $var = $this->getObjectAttributes();

            foreach ($var as &$value) {
                if (is_object($value) && method_exists($value,'toJSON')) {
                    $value = $value->toJSON();
                }
            }
            return $var;
        }

        public function getObjectAttributes() {
            //Retrieve attributes and remove the Mapper from the list
            $attributes = get_object_vars($this);
            unset($attributes["Mapper"]);

            return $attributes;
        }

        public function findById($id) {
            $mappedObject = $this->Mapper->findById($id);

            return $mappedObject;
        }

        public function getArray($wheres=null) {
            $objectArray = array();
            $objectArray = $this->Mapper->getObjectArray($wheres);

            return $objectArray;
        }

        public function getCurrent() {
            $mappedObject = $this->Mapper->getCurrentObject($this);

            return $mappedObject;
        }

        public function insert() {
            $this->Mapper->insertObject($this);
        }

        public function update() {
            $this->Mapper->updateObject($this);
        }

        public function delete() {
            $this->Mapper->deleteObject($this);
        }
        
        /** Generic getter/setter **/
        public function set($key, $value) {
            $this->$key = $value;
        }
        
        public function get ($key, $value) {
            return $this->$key;
        }

    }
?>
