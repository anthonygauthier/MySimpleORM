# MySimpleORM
MySimpleORM is a simple PHP/MySQL Object-relational mapping library. It started out as a personal and educational project, but turned into this little project you see here.

## Documentation

Still in the writing process!

## Requirements

To be able to use the ORM, you need to have a PHP application and a MySQL database. The requirements are quite simple. Create your object classes following this simple guideline.

1. Your PHP class must have the same name as your table. For instance; to be able to use your class "Users" you must have a table named "Users" in your MySQL schema.
2. Make sure to require the "BaseClass.php" file in your class and then extend your class with it.
3. Make sure that your class attributes are all equivalent to your table columns and ensure that they all have the same name.
4. Create your getters/setters.
5. Read the documentation (soon to be available) to understand how to select/update/delete/insert objects to your DB.
6. Don't forget to modify the "Database.php" class with your database information.

## Example of a class

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
