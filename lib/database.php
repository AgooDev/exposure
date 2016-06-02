<?php
/**
 * Copyright (c) 2016-present, Agoo.com.co <http://www.agoo.com.co>.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree or translated in the assets folder.
 */
require_once "../config/config.php";

class Database{

    private $connection;

    //Connect with database for mysql database
    public function __construct()
    {
        $this->connection = new mysqli(DB_CONNECTION,DB_USER,DB_PASSWORD);

        $this->connection->select_db(DB_NAME);

        //Check Connection
        if($this->connection->connect_errno){
            die("Connection Fail ".$this->connection->connect_error);
        }
        else{
            //echo "Connection is ok ";
        }
    }// End of constructor




    # Insert Data within table by accepting TableName and Table column => Data as associative array
    public function insert($tblname, array $val_cols){

        $keysString = implode(", ", array_keys($val_cols));

        $i=0;
        foreach($val_cols as $key=>$value) {
            $StValue[$i] = "'".$value."'";
            $i++;
        }

        $StValues = implode(", ",$StValue);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        //Perform Insert operation
        if($this->connection->query("INSERT INTO $tblname ($keysString) VALUES ($StValues)") === TRUE){
            //echo "New record has been inserted successfully!";
        }else{
            echo "Error ".$this->connection->error;
        }
        $last_id = $this->connection->insert_id;
        return $last_id;
    }//End of function insert




    //Delete data form table; Accepting Table Name and Keys=>Values as associative array
    public function delete($tblname, array $val_cols){
        //Append each element of val_cols associative array
        $i=0;
        foreach($val_cols as $key=>$value) {
            $exp[$i] = $key." = '".$value."'";
            $i++;
        }

        $Stexp = implode(" AND ",$exp);

        //Perform Delete operation
        if($this->connection->query("DELETE FROM $tblname WHERE $Stexp") === TRUE){
            if(mysqli_affected_rows($this->connection)){
                //echo "Record has been deleted successfully<br>";
            }
            else{
                //echo "The Record you want to delete is no loger exists<br>";
            }
        }
        else{
            echo "Error to delete".$this->connection->error;
        }
    }




    //Update data within table; Accepting Table Name and Keys=>Values as associative array
    public function update($tblname, array $set_val_cols, array $cod_val_cols){

        //append set_val_cols associative array elements
        $i=0;
        foreach($set_val_cols as $key=>$value) {
            $set[$i] = $key." = '".$value."'";
            $i++;
        }

        $Stset = implode(", ",$set);

        //append cod_val_cols associative array elements
        $i=0;
        foreach($cod_val_cols as $key=>$value) {
            $cod[$i] = $key." = '".$value."'";
            $i++;
        }

        $Stcod = implode(" AND ",$cod);

        //Update operation
        if($this->connection->query("UPDATE $tblname SET $Stset WHERE $Stcod") === TRUE){
            if(mysqli_affected_rows($this->connection)){
                echo "Record updated successfully<br>";
            }
            else{
                echo "The Record you want to updated is no loger exists<br>";
            }
        }else{
            echo "Error to update".$this->connection->error;
        }
    }

    //Call destructor function
    public function __destruct() {
        if($this->connection){
            // Close the connection
            $this->connection->close();
            //echo "Connection is release";
        }
    }

}
//end of class