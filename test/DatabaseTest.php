<?php
  declare(strict_types=1);
  
  require_once('./mapper/Database.php');
  use PHPUnit\Framework\TestCase;

  final class DatabaseTest extends TestCase {
      private $database;

      protected function setUp() {
          $this->database = new Database();
          $this->database->setup();
        }

      public function testConnect(): void {
        $this->assertNotNull($this->database->connect());
      }
  }
?>