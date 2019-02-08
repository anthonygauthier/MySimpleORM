<?php
  declare(strict_types=1);
  
  require_once('./src/mapper/ObjectMapping.php');
  require_once('./src/test/utilities/Users.php');

  use PHPUnit\Framework\TestCase;

  final class ObjectMappingTest extends TestCase {
      private $mapper;
      private $User;

      protected function setUp() {
          $this->User = new Users();
          $this->mapper = new ObjectMapping($this->User);
        }

      // public function testGetCurrent(): void {

      // }

      public function testSaveObject(): void {
        // insert
        $this->User->set("username", "user");
        $this->User->set("description", "desc");
        $this->User->save();
        $this->User->getCurrent();
        $this->assertGreaterThan()(0, $this->User->get("IDUsers"));

        // update
        $this->User->set("username", "user2");
        $this->User->save();
        $this->User->findById($this->User->get("IDUsers"));
        $this->assertEquals("user2", $this->User->get("username"));
      }

      // public function testSelectObject(): void {

      // }

      // public function testDeleteObject(): void {

      // }
  }
?>