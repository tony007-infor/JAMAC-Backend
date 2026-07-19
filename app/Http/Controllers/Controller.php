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
#[OA\Server(
    url: "http://3.17.70.189:8000", 
    description: "Servidor de Producción (AWS EC2)"
)]
abstract class Controller
{
    //
}
