<?php
class Connection 
{
    public $db;
    function __construct(){
        $this->db = new QueryManager("dbu1824788","Gw@o9JD62Qi5b","dbs15000644");
    }
}

?>