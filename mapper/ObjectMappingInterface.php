<?php
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