<?php
namespace App\Controllers;

use App\Core\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $data = [
            'title' => 'Inicio | MOCAPIC',
            'message' => 'Hola Mundo 🌎 — MOCAPIC está funcionando correctamente'
        ];

        $this->render('home', $data);
    }
}