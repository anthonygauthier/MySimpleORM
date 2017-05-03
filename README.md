# MySimpleORM
MySimpleORM is a simple PHP/MySQL Object-relational mapping library. It started out as a personal and educational project, but turned into this little project you see here.

* Straightforward usage, very simple as long as you follow the guidelines below.
* If your database contains foreign keys, the database abstraction layer will find them and add the joins to the query.
* Contains a method to echo your object as JSON, very useful for AJAX requests.
* Effective against SQL injections.

## Setting up MySimpleORM (MsORM)

To be able to use the ORM, you need to have a PHP application and a MySQL database. Follow these simple guidelines to setup MsORM on your PHP web application.

### Database-side guidelines

1. The names of your table are going to be the names of your object classes in PHP. Therefore, a table named "Users" will refer to the class "Users".
2. Make sure that your primary keys all start with "ID". For instance; "IDUsers".

### Application-side setup

1. Make sure to "require" the "BaseClass.php" file in your class file and then extend your class with it.
3. Make sure that your class attributes are all equivalent to your table columns and ensure that they all have the same name.
4. Create your getters/setters.
5. Read the (short) documentation to understand how to select/update/delete/insert objects to your DB.
6. Don't forget to modify the "Database.php" class with your database information.

#### Example of a class

```php
require_once("BaseClass.php");

class your_class extends BaseClass {
  public $IDyour_class;
  public $Name;

  public __construct() {
    $this->IDyour_class = 0;
    $this->Name = "";
  }
  public __destruct() {}

  public getIDyour_class() {
    return $this->IDyour_class;  
  }

  public getName() {
    return $this->Name;  
  }

  public setName($id) {
    $this->IDyour_class = $id;  
  }

  public setName($n) {
    $this->Name = $n;  
  }
}
```

## Documentation
*The examples below are all used as if they were part of a function within a controller.

### Select
#### To select an object by its ID
```php
$Users = new Users();
$Users = $Users->findObjectById(1);
```
You've retrieved the user "1" from the table "Users" and can now use it as an object.

#### To retrieve an array of objects
```php

$Users = new Users();
//replace by whatever condition you desire
$wheres = array(
  array (
    "column" => "IDCompanies",
    "condition" => "=",
    "value" => 1 //keep in mind you could use a sub-query here
  )
);
$Users = $Users->getObjectArray($wheres);
```
You've just retrieved all the users that were part of the company "1". You're object ```$Users``` is now an array of ```Users```

#### To retrieve current object, depending on what you've already set
Assuming the database contains a```Users``` with the name "foo".
```php

$Users = new Users();
$Users->setName("foo");
$Users = $Users->getCurrentObject();

//The mapper returned the object User named "foo"
``` 

### Insert
Inserting an object couldn't be any easier.
```php
$Users = new $Users();
$Users->setName("foo");
$Users->insertObject();
```
There you go, a new User has been added to your database.

### Update
Updating an object is just as simple as inserting it.
```php
$Users = new $Users();
$Users->setName("foo");
$Users->insertObject();

$Users->setName("bar");
$Users->updateObject();

//The newly added "foo" is now "bar"
```
### Delete

And now let's delete "bar" from the database.
```php
$Users = new $Users();
$Users->setName("foo");
$Users->insertObject();

$Users->setName("bar");
$Users->updateObject();

$Users->deleteObject();

//The newly updated "bar" is now deleted from the db
```
