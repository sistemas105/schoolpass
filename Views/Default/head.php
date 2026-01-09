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
    $user = Session::getSession('User');
    $userName = $user['full_name'] ?? 'Usuario';
    $userPhoto = $user['photo_path'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="<?php echo URL; ?>Main/Main">SchoolPass</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#responsiveNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="responsiveNavbar">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo URL; ?>Main/Main">Inicio</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button"
                       data-bs-toggle="dropdown">
                        Familia
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php echo URL; ?>Family/RegisterStudent">
                                Alumno
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL; ?>Family/RegisterRelative">
                                QR Familiar
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- PERFIL USUARIO -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                   href="#" data-bs-toggle="dropdown">

                    <img
                        src="<?php echo $userPhoto
                            ? htmlspecialchars($userPhoto)
                            : URL . 'Resource/images/user_default.png'; ?>"
                        width="36"
                        height="36"
                        class="rounded-circle me-2"
                        style="object-fit: cover;"
                    >

                    <strong><?php echo htmlspecialchars($userName); ?></strong>
                </a>

                <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                    <li>
                        <a class="dropdown-item" href="<?php echo URL; ?>Family/Profile" style="
    color: black;
">
                            👤 Mi perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?php echo URL; ?>Index/Logout">
                            🚪 Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>
<?php } ?>
<?php Session::star(); ?>
<?php if ($alert = Session::getSession('alert_message')): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: '<?= $alert['type']; ?>',
        title: '<?= $alert['title']; ?>',
        text: '<?= $alert['text']; ?>',
        confirmButtonColor: '#198754'
    });
});
</script>
<?php Session::delete('alert_message'); ?>
<?php endif; ?>