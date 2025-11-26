<?php

class Views 
{
    // Hacemos que los modelos sean opcionales asignándoles un valor predeterminado de null
    public function Render($controllers, $view, $model1 = null, $model2 = null, $model3 = null, $model4 = null, $model5 = null, $model6 = null)
    {
        $array = explode("Controller",get_class($controllers));
        $controller = $array[0];
        
        // El extract() es útil aquí para hacer $model1, $model2, etc. variables locales
        // Aunque no lo estás usando, la función ahora acepta llamadas con menos argumentos.
        
        require VIEWS.DFT."head.php";
        require VIEWS.$controller.'/'.$view.'.php';
        require VIEWS.DFT."footer.php";
    }

    // Aplicar el mismo principio al resto de métodos
    public function Render1($controllers, $view, $model1 = null, $model2 = null, $model3 = null, $model4 = null, $model5 = null, $model6 = null)
    {
        $array = explode("Controller",get_class($controllers));
        $controller = $array[0];
        require VIEWS.DFT."head1.php";
        require VIEWS.$controller.'/'.$view.'.php';
        require VIEWS.DFT."footer.php";
    }
    
    // Este método espera 9 argumentos, hacemos el último también opcional
    public function Render2($controllers, $view, $model1 = null, $model2 = null, $model3 = null, $model4 = null, $model5 = null, $model6 = null, $model7 = null)
    {
        $array = explode("Controller",get_class($controllers));
        $controller = $array[0];
        require VIEWS.DFT."head.php";
        require VIEWS.$controller.'/'.$view.'.php';
        require VIEWS.DFT."footer.php";
    }
}

?>