<?php
class AuthController {
    public function login() {
        include '../app/views/auth/login.php';
    }

    public function register() {
        include '../app/views/auth/register.php';
    }
}
