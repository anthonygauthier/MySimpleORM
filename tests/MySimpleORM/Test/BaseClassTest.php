<?php
  namespace MySimpleORM\Test;
  
  use PHPUnit\Framework\TestCase;

  $createdId = 0;

  final class BaseClassTest extends TestCase {
    private $User;

    protected function setUp() {
      $this->User = new Users();
    }

    public function testORM(): void {
      // insert assertion
      $this->User->set("username", "user");
      $this->User->set("description", "desc");
      $this->User->save();
      $this->User->getCurrent();
      $createdId = (int) $this->User->get("IDUsers");
      
      $this->User->set("username", "user2");
      $this->User->save();
      $this->User = $this->User->findById($createdId);

      // select assertion
      $this->assertNotNull($this->User);

       // update assertion
      $this->assertGreaterThan(0, $createdId);
      $this->assertEquals("user2", $this->User->get("username"));

      // delete assertion
      $this->User->delete();
      var_dump($this->User);
      $this->assertEquals(0, $this->User->get("IDUsers"));
    }
  }
?>