<?php
namespace MySimpleORM;

use MySimpleORM\Mapper\ObjectMapping;

class BaseClass
{
    protected $Mapper;

    public function __construct()
    {
        $this->Mapper = new ObjectMapping($this);
    }

    public function __destruct()
    {}

    public function toJSON()
    {
        $var = $this->getObjectAttributes();

        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value, 'toJSON')) {
                $value = $value->toJSON();
            }
        }
        return $var;
    }

    public function getObjectAttributes()
    {
        // clean up attributes
        $attributes = get_object_vars($this);
        unset($attributes["Mapper"]);
        unset($attributes["Object"]);
        unset($attributes["ClassName"]);
        unset($attributes["PrimaryKey"]);
        unset($attributes["ForeignKeys"]);

        return $attributes;
    }

    public function findById($id)
    {
        $mappedObject = $this->Mapper->findById($id);

        return $mappedObject;
    }

    public function getArray($wheres = null)
    {
        $objectArray = array();
        $objectArray = $this->Mapper->getObjectArray($wheres);

        return $objectArray;
    }

    public function getCurrent()
    {
        $mappedObject = $this->Mapper->getCurrentObject($this);

        return $mappedObject;
    }
    // DEPRECATED
    public function insert()
    {
        printf("DEPRECATED: The \"insert()\" method is deprecated since version 2.0. Please use \"save()\" instead.");
        $this->Mapper->insertObject($this);
    }
    // DEPRECATED
    public function update()
    {
        printf("DEPRECATED: The \"insert()\" method is deprecated since version 2.0. Please use \"save()\" instead.");
        $this->Mapper->updateObject($this);
    }

    public function save()
    {
        $this->Mapper->saveObject($this);
    }

    public function delete()
    {
        $this->Mapper->deleteObject($this);
        $this->__destruct();
        gc_collect_cycles();
    }

    /** Generic getter/setter **/
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    public function get($key)
    {
        return $this->$key;
    }

}
