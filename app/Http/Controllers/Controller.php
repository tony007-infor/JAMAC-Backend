<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Phoenix Orders - API JAMAC",
    version: "1.0.0",
    description: "Documentación de la API para la gestión de Clientes, Productos y Pedidos."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor de Desarrollo Local"
)]
abstract class Controller
{
    //
}
