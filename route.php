<?php

use App\Service\Database;

$databaseService = new Database();
$route = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route service implemented. Not advanced but does the trick.
switch ($route) {
    // For Main Page, route can accept '/' or '' or query strings prepended by '?'.
    case '/':
    case (bool) preg_match('/\/\?/i', $route):
    case '':
        showMain($databaseService);

        break;

    // Implemented POST method, throws 404 if called by GET method.
    case '/migrate':
        if ($requestMethod !== 'POST') {
            show404();

            break;
        }

        migrate($databaseService);

        break;

    // If no case match, throws 404.
    default:
        show404();

        break;
}

/**
 * Route to include files for main page.
 */
function showMain($databaseService)
{
    if (!$databaseService->getTablesCount()[0]['total']) {
        require __DIR__ . '/views/setup.php';
    } else {
        require __DIR__ . '/views/main.php';
    }
}

/**
 * Route to include 404 error page.
 */
function show404()
{
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}

/**
 * Route to migrate and seed.
 */
function migrate($databaseService)
{
    // Returning JSON response so setting up the header here.
    header('Content-Type: application/json; charset=utf-8');

    // Migrate and seed.
    $databaseService->createTable();
    $databaseService->seedData();
}
