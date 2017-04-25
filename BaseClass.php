<?php
    /*
    * Author      : Anthony Gauthier
    * Owner       : Anthony Gauthier
    * Date created  : 2017-04-04
    * Date modified : 2017-04-04
    * Software    : YOUR PROJECT
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

        public function findObjectById($id) {
            $mappedObject = $this->Mapper->findById($id);

            return $mappedObject;
        }

        public function getObjectArray() {
            $objectArray = array();
            $objectArray = $this->Mapper->getObjectArray();

            return $objectArray;
        }

        public function getCurrentObject() {
            $mappedObject = $this->Mapper->getCurrentObject($this);

            return $mappedObject;
        }

        public function insertObject() {
            $this->Mapper->insertObject($this);
        }

        public function updateObject() {
            $this->Mapper->updateObject($this);
        }

        public function deleteObject() {
            $this->Mapper->deleteObject($this);
        }

    }
?>