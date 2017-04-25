<?php
/*
* Author      : Anthony Gauthier
* Owner       : Anthony Gauthier
* Date created  : 2017-03-19
* Date modified : 2017-04-04
* Software    : YOUR PROJECT
* File        : Database.php
* Description : Database wrapper
*/
    // namespace ObjectMapping;

    class Database {
        /**
         * Variables
         * 
         * @var [string] $Host
         * @var [string] $Database
         * @var [string] $User
         * @var [string] $Password
         * @var [mysqli] $Instance
         */
        protected $Host;
        protected $Database;
        protected $User;
        protected $Sql;
        protected $Password;
        protected $Instance;

        /**
         * Database::__construct()
         * Constructor & destructor
         */
        public function __construct() {
            $this->Host = "SERVER_HOST";
            $this->User = "MYSQL_USER";
            $this->Password = "MYSQL_PASSWORD";
            $this->Database = "MYSQL_DATABASE";
        }

        public function __destruct() {}
        
        /**
         * Database::connect()
         * Opens up the connection to the database based on the object's attributes¸
         * 
         * @return void
         */
        public function connect() {
            
            $con = mysqli_connect($this->Host, $this->User, $this->Password, $this->Database, "3306");

            if(mysqli_connect_errno()) {
                die($this->getError());
            } else {
                $this->Instance = $con;
            }
            
            return $this->Instance;
        }

        
        /**
         * Database::disconnect()
         * Closes the database's connection to avoid conflict and release memory
         * 
         * @return void
         */
        public function disconnect() {
            if(!mysqli_close($this->Instance)) {
                die($this->getError());
            }
        }

        /**
         * Database::select()
         * Format SQL and returns query content
         * 
         * @param [string] $table
         * @param [array] $columns
         * @param [array] $wheres
         * @param [array] $joins
         * @param [array] $orderbys
         * @return [array] $results
         */
        public function select($table, $columns=null, $wheres=null, $joins=null, $orderbys=null) {
            //Variables
            $return = array();

            if($table != "" || $table != null) {
                 //Open up connection
                $this->connect();
                
                //SELECT statement
                $this->Sql = "SELECT ";

                //COLUMNS
                if($columns != "" || $columns != null) {
                    foreach($columns as $index=>$column) {
                        if($index == 0) {
                            $this->Sql .= $column;
                        } else {
                            $this->Sql .= ", " . $column;
                        }
                    }
                } else {
                    $this->Sql .= "*";
                }

                //FROM statement
                $this->Sql .= " FROM " . $table;

                //JOINS
                if($joins != "" || $joins != null) {
                    foreach($joins as $join) {
                        $this->Sql .= $join["orientation"] . " JOIN " . $join["referenced_table"] . " ON " . $join["table_name"] . "." . $join["column_name"] . " = " . $join["referenced_table"] . "." . $join["referenced_column"];
                    }
                }

                //WHERE statement
                if($wheres != "" || $wheres != null) {
                    foreach($wheres as $index=>$where) {
                        //if string format SQL
                        if(gettype($where["value"]) == "string" && !is_numeric($where["value"])) {
                            $where["value"] = "'".$where["value"]."'";
                            $where["value"] = htmlentities(utf8_encode($where["value"]));
                            $where["condition"] = "LIKE";
                        } 

                        if($index == 0) {
                            $this->Sql .= " WHERE " . $where["column"] . " " . $where["condition"] . " " . $where["value"];
                        } else {
                            if(sizeof($wheres) == $index) {
                                $this->Sql .= " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            } else {
                                $this->Sql .= " " . $where["operation"] . " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            }
                        }
                    }
                }

                //ORDER BY statement
                if($orderbys != "" || $orderbys != null) {
                    foreach($orderbys as $index=>$orderby) {
                        if($index == 0) {
                            $this->Sql .= " ORDER BY " . $orderby;
                        } else {
                            $this->Sql .= ", " . $orderby;
                        }
                    }
                }
                
                $this->Sql .= ";";

                $results = $this->Instance->query($this->Sql);

                if(!$results) {
                    die($this->getError());
                }

                while($row = $results->fetch_assoc()) {
                    $return[] = $row;
                }

                array_walk_recursive($return, function(&$item, $key){
					$item = html_entity_decode(utf8_decode($item));
				});

                $this->disconnect();
                
                return $return;
            } else {
                die("Must provide a table to query the database.");
            }
        }

        /**
         * Database::insert()
         * Format SQL and inserts into database
         * 
         * @param [type] $table
         * @param [type] $columns
         * @param [type] $values
         * @return void
         */
        public function insert($table, $columns, $values) {
            if($table != "" || $table != null) {
                 $this->connect();
                 $this->Sql = "INSERT INTO " . $table; 

                 if(($columns != "" || $columns != null) || sizeof($columns) != sizeof($values)) {
                    $this->Sql .= "(";
                    
                    //COLUMNS
                    foreach($columns as $index=>$column) {
                        if($index == 0) {
                            $this->Sql .= $column;
                        } else {
                            $this->Sql .= ", " . $column;                           
                        }
                    }
                    
                    $this->Sql .= ") VALUES (";

                    if($values != "" || $values != null) { 

                        //VALUES
                        foreach($values as $index=>$value) {
                            if(gettype($value) == "string" && !is_numeric($value)) {
                                $value = "'".trim(htmlentities(utf8_encode($value)))."'";
                            }
                            
                            if($index == 0) {
                                $this->Sql .= $value;
                            } else {
                                $this->Sql .= ", " . $value; 
                            }
                        }

                        $this->Sql .= ");";

                        //insert into database
                        if(!$this->Instance->query($this->Sql)) {
                            die($this->getError());
                        }
                    }
                 } else {
                     die("Either set columns or set the same amount of columns/values to insert");
                 }

                 $this->disconnect();
            } else {
                die("Must provide a table to insert to");
            }
        }

        /**
         * Database::delete()
         * Format SQL and deletes the specific row in the database
         * 
         * @param [string] $table
         * @param [array] $columns
         * @param [array] $values
         * @return [boolean] ? (true) row deleted : error;
         */
        public function delete($table, $wheres=null) {
            if($table != "" || $table != null) {
                 $this->connect();
                 $this->Sql = "DELETE FROM " . $table; 

                //WHERE
                if($wheres != "" || $wheres != null) {
                    foreach($wheres as $index=>$where) {
                        //if string format SQL
                        if(gettype($where["value"]) == "string" && !is_numeric($where["value"])) {
                            $where["value"] = "'".trim($where["value"])."'";
                            $where["value"] = htmlentities(utf8_encode($where["value"]));
                            $where["condition"] = "LIKE";
                        } 

                        if($index == 0) {
                            $this->Sql .= " WHERE " . $where["column"] . " " . $where["condition"] . " " . $where["value"];
                        } else {
                            if(sizeof($wheres) == $index) {
                                $this->Sql .= " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            } else {
                                $this->Sql .= " " . $where["operation"] . " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            }
                        }
                    }
                }

                $this->Sql .= ";";

                //delete from database
                if(!$this->Instance->query($this->Sql)) {
                    die($this->getError());
                }

                 $this->disconnect();
            } else {
                die("Must provide a table to delete from");
            }
        }

        /**
         * Database::update()
         * Format SQL and updates the specific row in the database
         * 
         * @return [boolean] ? (true) row updated : error;
        */
        public function update($table, $columns, $values, $wheres=null) {
            if($table != "" || $table != null) {
                 $this->connect();
                 $this->Sql = "UPDATE " . $table; 

                //SET columns, [...]
                if(($columns != "" || $columns != null) || sizeof($columns) != sizeof($values)) {
                    $this->Sql .= " SET ";
                    
                    //COLUMNS
                    foreach($columns as $index=>$column) {
                        if(gettype($values[$index]) == "string" && !is_numeric($values[$index])) {
                            $values[$index] = "'".trim($values[$index])."'";
                            $values[$index] = htmlentities(utf8_encode($values[$index]));
                        }

                        if($index == 0) {
                            $this->Sql .= "" . $column . " = " . $values[$index];
                        } else {
                            $this->Sql .= ", " . $column  . " = " . $values[$index];                           
                        }
                    }
                 } else {
                     die("Either set columns or set the same amount of columns/values to insert");
                 }

                //WHERE
                if($wheres != "" || $wheres != null) {
                    foreach($wheres as $index=>$where) {
                        //if string format SQL
                        if(gettype($where["value"]) == "string" && !is_numeric($where["value"])) {
                            $where["value"] = "'".$where["value"]."'";
                            $where["value"] = htmlentities(utf8_encode($where["value"]));
                            $where["condition"] = "LIKE";
                        } 

                        if($index == 0) {
                            $this->Sql .= " WHERE " . $where["column"] . " " . $where["condition"] . " " . $where["value"];
                        } else {
                            if(sizeof($wheres) == $index) {
                                $this->Sql .= " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            } else {
                                $this->Sql .= " " . $where["operation"] . " " . $where["column"] . " " . $where["condition"] . " " . $where["value"]; 
                            }
                        }
                    }
                }

                $this->Sql .= ";";

                //update in database
                if(!$this->Instance->query($this->Sql)) {
                    die($this->getError());
                }

                 $this->disconnect();
            } else {
                die("Must provide a table to delete from");
            }
        }

        //TODO. Check error with ObjectMapping class
        public function getJoinsArray($table) {
            $fk_array = array();
            $return = array();
            $this->Sql = "SHOW CREATE TABLE " . $table;

            $results = $this->Instance->query($this->Sql);

            if(!$results) {
                die($this->getError());
            }

            while($row = $results->fetch_assoc()) {
                $fk_array[] = $row;
            }

            $fk_array = explode("FOREIGN KEY", $fk_array[0]["Create Table"]);

            foreach($fk_array as $fk) {
                if(strpos($fk, "REFERENCES")) {
                    //Isolate referenced table
                    $referenced_table = substr($fk, strpos($fk, "REFERENCES"));
                    $referenced_table = substr($referenced_table, strpos($referenced_table, "`") + 1);
                    $referenced_table = substr($referenced_table, 0, strpos($referenced_table, "`"));
                    //Isolate referenced column
                    $referenced_column = substr($fk, strrpos($fk, "(") + 2);
                    $referenced_column = substr($referenced_column, 0, strpos($fk, ")") - 4);
                    //Isolate current table's column (should be the same as referenced)
                    $column_name = substr($fk, 3, strpos($fk, ")") - 4);

                    $return[] = array(
                        "orientation"       => "INNER",
                        "referenced_table"  => $referenced_table,
                        "referenced_column" => $referenced_column,
                        "column_name"       => $column_name,
                        "table_name"        => $table
                    );
                }
            }

            return $return;
        }

        public function getSql() {
            return $this->Sql;
        }

        private function getError() {
            return "<br/>" . $this->Sql . "<br/> SQL Exception #" . $this->Instance->errno . " : " . $this->Instance->error . "<br/>";
        }
    }
?>