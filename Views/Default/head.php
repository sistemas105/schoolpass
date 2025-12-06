<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>School Pass</title>
 
   <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous"
>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
 <link rel="stylesheet" href="<?php echo URL . RQ ?>css/style.css" />
</head>

<?php
    if (Session::getSession('User')) {
        $userName = Session::getSession('User')['full_name'] ?? 'Usuario';
?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="<?php echo URL; ?>Main/Main">SchoolPass</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#responsiveNavbar" aria-controls="responsiveNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="responsiveNavbar">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo URL; ?>Main/Main">Inicio</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Familia
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo URL; ?>Family/RegisterStudent">Dar alta Alumno</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo URL; ?>Family/RegisterRelative">Dar de alta Familiar</a>
                            </li>
                            </ul>
                    </li>
                    </ul>
                
                <div class="d-flex align-items-center">
                    <span class="navbar-text me-3 pe-2 text-white">
                        ¡Hola, **<?php echo htmlspecialchars($userName); ?>**!
                    </span>
                    
                    <a class="btn btn-outline-danger" href="<?php echo URL; ?>Index/Logout">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>
    <?php
    } // Cierre del if
?>