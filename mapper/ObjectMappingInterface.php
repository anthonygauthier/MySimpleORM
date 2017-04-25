<?php
/*
* Author      : Anthony Gauthier
* Owner       : Anthony Gauthier
* Date created  : 2017-03-24
* Date modified : 2017-03-24
* Software    : YOUR PROJECT
* File        : ObjectMappingInterface.php
* Description : Object Mapping Interface
*/
    /**
     * Interface defining all the methods the ObjectMapping class 
     * will be using
     */
    interface ObjectMappingInterface { 
        public function findById($id);
        public function findByParam($array);
        public function getCurrentObject();
        public function getObjectArray($wheres);
        public function insertObject();
        public function updateObject();
        public function deleteObject();
    }
?>