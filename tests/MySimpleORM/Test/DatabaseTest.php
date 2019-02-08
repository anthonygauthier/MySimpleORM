<?php
  namespace MySimpleORM\Test;

  use MySimpleORM\Mapper\Database;
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