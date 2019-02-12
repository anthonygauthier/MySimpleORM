<?php
namespace MySimpleORM\Test;

require 'src/MySimpleORM/Autoload.php';
use MySimpleORM\BaseClass;

class Users extends BaseClass
{
    private $IDUsers;
    private $username;
    private $description;

    public function __construct()
    {
        parent::__construct();
        $this->IDUsers = 0;
        $this->username = "";
        $this->description = "";
    }

    public function __destruct()
    {}
}
