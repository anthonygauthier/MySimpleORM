<?php
  namespace MySimpleORM\Test;
  
  use MySimpleORM\Utils\Transform;
  use PHPUnit\Framework\TestCase;

  final class TransformTest extends TestCase {
    private $shorthand_wheres;

    protected function setUp() {
      $this->shorthand_wheres = array(
        "IDCompanies,=,10",
        "IDCompanies,>=,50"
      );
    }

    public function test_split_conditions(): void {
      $transformed = Transform::split_conditions($this->shorthand_wheres);
      var_dump($transformed);
      $this->assertNotNull($transformed);
    }
  }
?>