<?php
  namespace MySimpleORM\Test;
  
  use PHPUnit\Framework\TestCase;

  $createdId = 0;

  final class BaseClassTest extends TestCase {
    private $User;

    protected function setUp() {
      $this->User = new Users();
      if(getenv("TRAVIS") == true) {
        $this->Users->Mapper->Database->setup("127.0.0.1", "root", "", "travis", "3306");
      }
    }

    public function testSave(): void {
      // insert assertion
      $this->User->set("username", "user");
      $this->User->set("description", "desc");
      $this->User->save();
      $this->User->getCurrent();
      $createdId = (int) $this->User->get("IDUsers");
      
      // update assertion
      $this->User->set("username", "user2");
      $this->User->save();
      $this->User = $this->User->findById($createdId);

      $this->assertGreaterThan(0, $createdId);
      $this->assertEquals("user2", $this->User->get("username"));
    }
  }
?>