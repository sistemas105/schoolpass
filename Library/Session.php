<?php
class Session 
{
    /**
     * Inicia la sesión de forma segura.
     */
    static function star(){
        // Usar session_status() para evitar advertencias si ya está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }
    
    /**
     * Obtiene el valor de una variable de sesión.
     */
    static function getSession($name){
        if(isset($_SESSION[$name])){
            return $_SESSION[$name];
        }
    }
    
    /**
     * Establece el valor de una variable de sesión.
     */
    static function setSession($name,$data){
        return $_SESSION[$name] = $data;
    }
    
    /**
     * Elimina una variable de sesión específica.
     * ⭐️ MÉTODO AGREGADO PARA RESOLVER EL ERROR ⭐️
     */
    static function delete($name){
        if(isset($_SESSION[$name])){
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }
    
    /**
     * Destruye todas las variables de sesión.
     */
    static function destroy(){
        @session_destroy();
    }
}