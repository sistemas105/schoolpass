<?php
class QueryManager 
{
   public $pdo;
    // ⚠️ Asegúrate que este offset sea el correcto para tu zona horaria
    private const TIMEZONE = '-06:00'; 
    
    function __construct($USER,$PASS,$DB){
        try {
            $this->pdo = new PDO('mysql:host=db5019071793.hosting-data.io;dbname='.$DB.';charset=utf8'
            ,$USER, $PASS,[
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // ⭐️ CÓDIGO AÑADIDO: Establece la zona horaria de la sesión de MySQL ⭐️
            $this->pdo->exec("SET time_zone = '" . self::TIMEZONE . "';");
            // ⭐️ FIN CÓDIGO AÑADIDO ⭐️

        } catch (Throwable $th) {
            print "¡Error!: " . $th->getMessage() ;
            die();
        }
    }
    function select1($attr,$table,$where,$param){
        try{
            $where = $where ?? "";        
            $query = "SELECT ".$attr." FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            return $e->getMessage();
        }
        $pdo = null;
    }
    function select3($attr,$table,$where,$param){
        try{
            $where = $where ?? "";        
            $query = "SELECT ".$attr." FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            return $e->getMessage();
        }
        $pdo = null;
    }
    function select4($attr,$where,$param){
        try{
            $where = $where ?? "";        
            $query = "SELECT ".$attr." FROM ".$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            return $e->getMessage();
        }
        $pdo = null;
    }
   function insert($table, $param, $value){
    try{
        $query = "INSERT INTO ".$table.$value;
        $sth = $this->pdo->prepare($query);
        $sth->execute((array)$param);
        
        // Retorna TRUE solo si se insertó al menos una fila.
        return $sth->rowCount() > 0; 
        
    }catch (PDOException $e){
        // Muestra el error de SQL en el log del servidor
        error_log("SQL INSERT ERROR: " . $e->getMessage()); 
        // Retorna false para que el controlador muestre el mensaje de error de SweetAlert
        return false; 
    }
}
    function update($table,$param,$value,$where){
        try{
            $query = "UPDATE ".$table." SET ".$value.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute((array)$param);
            return true;
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }
    function delete($table,$where,$param){
        try{
            $query = "DELETE FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            return true;
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }
    function Select2($attr,$table,$pagi_inicial,$pagi_cuantos,$where,$param){
        try{
            $query = "SELECT ".$attr." FROM ".$table.$where." LIMIT $pagi_inicial,$pagi_cuantos";
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            return $e->getMessage();
        }
        $pdo = null;
    }
}

?>