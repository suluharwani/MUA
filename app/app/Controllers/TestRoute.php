<?php
// app/Controllers/TestRoute.php
namespace App\Controllers;

class TestRoute extends BaseController
{
    public function index()
    {
        echo "<h1>Testing Routes</h1>";
        
        // Test jika route paket ada
        $routes = service('routes');
        $routesList = [];
        
        foreach ($routes->getRoutes('get') as $route => $handler) {
            if (strpos($route, 'admin/paket') !== false) {
                $routesList[] = $route . ' => ' . (is_string($handler) ? $handler : gettype($handler));
            }
        }
        
        foreach ($routes->getRoutes('post') as $route => $handler) {
            if (strpos($route, 'admin/paket') !== false) {
                $routesList[] = $route . ' => ' . (is_string($handler) ? $handler : gettype($handler));
            }
        }
        
        echo "<h3>Paket Routes:</h3>";
        echo "<ul>";
        foreach ($routesList as $route) {
            echo "<li>$route</li>";
        }
        echo "</ul>";
        
        // Test controller method
        echo "<h3>Testing Controller Method:</h3>";
        
        if (method_exists('App\Controllers\Admin\Paket', 'bulkAction')) {
            echo "<p style='color: green;'>✓ Method bulkAction exists in Paket controller</p>";
        } else {
            echo "<p style='color: red;'>✗ Method bulkAction NOT found in Paket controller</p>";
        }
        
        if (method_exists('App\Controllers\Admin\Paket', 'index')) {
            echo "<p style='color: green;'>✓ Method index exists in Paket controller</p>";
        } else {
            echo "<p style='color: red;'>✗ Method index NOT found in Paket controller</p>";
        }
    }
}