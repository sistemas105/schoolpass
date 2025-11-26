<?php
class Controllers 
{
    // === Solución: Declarar propiedades aquí ===
    public $view; 
    public $model; 
    // ===========================================

    public function __construct() {
        date_default_timezone_set('America/Mexico_City');
        Session::star();
        $this->view = new Views();     
        
        $this->loadClassmodels();
    }
    public function loadClassmodels()
    {
        $array = explode("Controller",get_class($this));
        $model = $array[0].'_model';
        $path = 'Models/'.$model.'.php';
        if(file_exists($path)){
            require $path;
            $this->model = new $model();
        }
    }
}
?>