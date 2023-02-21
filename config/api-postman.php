<?php

return [

    /*
     * Structured.
     *
     * If you want folders to be generated based on namespace.
     */

    'structured' => true,

    /*
     * Base URL.
     *
     * The base URL for all of your endpoints.
     */

    'base_url' => env('APP_URL', 'https://corvus.test'),

    /*
     * Auth Middleware.
     *
     * The middleware which wraps your authenticated API routes.
     *
     * E.g. auth:api, auth:sanctum
     */

    'auth_middleware' => 'auth:api',

];
