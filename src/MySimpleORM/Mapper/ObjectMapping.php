<?php
namespace MySimpleORM\Mapper;

use MySimpleORM\Mapper\Database;
use MySimpleORM\Mapper\ObjectMappingInterface;

class ObjectMapping implements ObjectMappingInterface
{
    /**
     * Variables
     *
     * @var [Database] $Database
     * @var [object] $Object
     */
    protected $Database;
    protected $Object;
    protected $ClassName;
    protected $PrimaryKey;
    protected $ForeignKeys;

    /**
     * ObjectMapping::__construct()
     * Constructor & destructor
     * @param [string] $t
     * @param [object] $o
     */
    public function __construct($o)
    {
        $classPathArray = explode("\\", get_class($o));
        $this->ClassName = $classPathArray[sizeof($classPathArray) - 1];
        $this->Database = new Database();
        $this->Object = $o;
        if ($this->Database->connect()) {
            $this->PrimaryKey = $this->Database->getKeys($this->ClassName, "primary");
            $this->ForeignKeys = $this->Database->getKeys($this->ClassName, "foreign");
        }
    }

    public function __destruct()
    {}

    /**
     * Retrieves a record in the database by ID
     *
     * @param [int] $id
     * @return $this->Object
     */
    public function findById($id)
    {
        $where = array(
            array(
                "column" => $this->PrimaryKey,
                "value" => $id,
                "condition" => "=",
            ),
        );

        if ($this->Database->connect()) {
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
    public function getObjectArray($wheres = null)
    {
        $return = array();
        $rows = array();

        if ($this->Database->connect()) {
            $rows = $this->Database->select($this->ClassName, null, $wheres, $this->Database->getJoinsArray($this->ClassName));
            $this->mapObject($this->Object, $rows, $return);
        }

        return $return;
    }

    /**
     * getCurrentObject
     *
     * @param [type] $obj
     * @return void
     */
    public function getCurrentObject($obj = null)
    {
        $objectAttributes = $this->Object->getObjectAttributes($obj);
        $wheres = array();

        foreach ($objectAttributes as $attributeName => $attributeValue) {
            if (($attributeName != $this->PrimaryKey) && ($attributeValue != null || $attributeValue != 0 || $attributeValue != "")) {
                array_push($wheres, array(
                    "column" => $attributeName,
                    "value" => $attributeValue,
                    "condition" => "=",
                    "operation" => "AND",
                ));
            }
        }

        if ($this->Database->connect()) {
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
    public function insertObject($obj = null)
    {
        $objectAttributes = $this->Object->getObjectAttributes($obj);
        $columns = array();
        $values = array();

        foreach ($objectAttributes as $attributeName => $attributeValue) {
            if ($attributeName !== $this->PrimaryKey) {
                array_push($columns, $attributeName);
                array_push($values, $this->Object->get($attributeName));
            }
        }

        $this->Database->insert($this->ClassName, $columns, $values);
    }

    /**
     * updateObject
     *
     * @param [type] $obj
     * @return void
     */
    public function updateObject($obj = null)
    {
        $columns = array();
        $values = array();
        $where = array(
            array(
                "column" => $this->PrimaryKey,
                "value" => $this->Object->get($this->PrimaryKey),
                "condition" => "=",
            ),
        );

        $objectAttributes = $this->Object->getObjectAttributes($obj);

        foreach ($objectAttributes as $attributeName => $attributeValue) {
            if ($attributeName != $this->PrimaryKey) {
                array_push($columns, $attributeName);
                array_push($values, $this->Object->get($attributeName));
            }
        }

        $this->Database->update($this->ClassName, $columns, $values, $where);
    }

    /**
     * saveObject
     *
     * @return void
     */
    public function saveObject()
    {
        if (($this->Object->get($this->PrimaryKey) != 0)) {
            $this->updateObject($this->Object);
        } else {
            $this->insertObject($this->Object);
        }
    }

    /**
     * deleteObject
     *
     * @param [type] $obj
     * @return void
     */
    public function deleteObject()
    {
        $where = array(
            array(
                "column" => $this->PrimaryKey,
                "value" => $this->Object->get($this->PrimaryKey),
                "condition" => "=",
            ),
        );

        $this->Database->delete($this->ClassName, $where);
        $this->Object->set($this->PrimaryKey, 0);
    }
    /**
     * Undocumented function
     *
     * @param [type] $obj - referenced object
     * @param [type] $rows - rows retrived by DB
     * @param [type] $objArr - referenced object array
     * @return void
     */
    public function mapObject(&$obj, $rows, &$objArr = null)
    {
        $attributes = $obj->getObjectAttributes($obj);

        foreach ($rows as $row) {
            // reset the object reference
            $class = get_class($obj);
            $this->Object = new $class();
            
            foreach ($attributes as $key => $attribute) {
                //If object contains other objects
                if (in_array($key, $this->ForeignKeys) && $key != $this->PrimaryKey) {
                    $linkedClassName = str_replace("ID", "", $key);
                    $objectToPush = new $linkedClassName();
                    $objectToPush = $objectToPush->findObjectById($row[$key]);
                    $obj->set($key, $objectToPush);
                } else {
                    $obj->set($key, $row[$key]);
                }
            }

            //add to array if array isset - getObjectArray()
            if (isset($objArr)) {
                $objArr[] = $obj;
            }
        }
    }
}
