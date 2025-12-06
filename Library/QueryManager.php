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

    /**
     * Ejecuta una consulta SELECT simple.
     * Siempre retorna un array asociativo con la clave 'results'.
     */
    function select1($attr,$table,$where,$param){
        try{
            $where = $where ?? "";      
            $query = "SELECT ".$attr." FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            // 🚨 CORRECCIÓN: Devolver array vacío en caso de error de DB 
            error_log("DB Select1 Error: " . $e->getMessage()); 
            return array("results" => []);
        }
    }
    
    /**
     * Ejecuta una consulta SELECT simple. (Alias para select1)
     * Siempre retorna un array asociativo con la clave 'results'.
     */
    function select3($attr,$table,$where,$param){
        try{
            $where = $where ?? "";      
            $query = "SELECT ".$attr." FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            // 🚨 CORRECCIÓN: Devolver array vacío en caso de error de DB 
            error_log("DB Select3 Error: " . $e->getMessage()); 
            return array("results" => []);
        }
    }

    /**
     * Ejecuta una consulta SELECT compleja (donde $where incluye la tabla).
     * Siempre retorna un array asociativo con la clave 'results'.
     */
    function select4($attr,$where,$param){
        try{
            $where = $where ?? "";      
            $query = "SELECT ".$attr." FROM ".$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            // 🚨 CORRECCIÓN: Devolver array vacío en caso de error de DB
            error_log("DB Select4 Error: " . $e->getMessage()); 
            return array("results" => []);
        }
    }

    /**
     * Ejecuta una inserción de datos.
     * @return bool True si la inserción fue exitosa (rowCount > 0), false en caso contrario o error.
     */
    function insert($table, $param, $value){
        try{
            $query = "INSERT INTO ".$table.$value;
            
            $sth = $this->pdo->prepare($query);
            $sth->execute((array)$param);
            
            // Retorna TRUE solo si se insertó al menos una fila.
            return $sth->rowCount() > 0; 
            
        }catch (PDOException $e){
            error_log("SQL INSERT ERROR: " . $e->getMessage()); 
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
            error_log("SQL UPDATE Error: " . $e->getMessage());
            return false;
        }
    }

    function delete($table,$where,$param){
        try{
            $query = "DELETE FROM ".$table.$where;
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            return true;
        }catch (PDOException $e){
            error_log("SQL DELETE Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecuta una consulta SELECT con LIMIT para paginación.
     * Siempre retorna un array asociativo con la clave 'results'.
     */
    function Select2($attr,$table,$pagi_inicial,$pagi_cuantos,$where,$param){
        try{
            $where = $where ?? ""; 
            $query = "SELECT ".$attr." FROM ".$table.$where." LIMIT $pagi_inicial,$pagi_cuantos";
            $sth = $this->pdo->prepare($query);
            $sth->execute($param);
            $response = $sth->fetchAll(PDO::FETCH_ASSOC);
            return array("results" => $response);
        }catch (PDOException $e){
            // 🚨 CORRECCIÓN: Devolver array vacío en caso de error de DB
            error_log("DB Select2 Error: " . $e->getMessage()); 
            return array("results" => []);
        }
    }
}