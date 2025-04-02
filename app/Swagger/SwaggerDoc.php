<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API PAL",
 *         version="1.0.0",
 *         description="Documentation de l'API",
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Serveur local"
 *     )
 * )
 */
class SwaggerDoc {}
