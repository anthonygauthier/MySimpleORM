<?php
  declare(strict_types=1);

  require_once('./test/utilities/Users.php');

  use PHPUnit\Framework\TestCase;

  $createdId = 0;

  final class BaseClassTest extends TestCase {
    private $User;

    protected function setUp() {
      $this->User = new Users();
    }

    public function testSave(): void {
      // insert assertion
      $this->User->set("username", "user");
      $this->User->set("description", "desc");
      $this->User->save();
      $this->User->getCurrent();
      $createdId = (int) $this->User->get("IDUsers");
      printf($createdId . "\n");
      
      // update assertion
      $this->User->set("username", "user2");
      printf($createdId . "\n");
      $this->User->save();
      printf($createdId . "\n");
      $this->User = $this->User->findById($createdId);

      $this->assertGreaterThan(0, $createdId);
      $this->assertEquals("user2", $this->User->get("username"));
    }
  }
?>