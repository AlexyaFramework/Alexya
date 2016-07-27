<?php
/**
 * Alexya Framework - The intelligent Loli Framework
 *
 * This file contains the default routes.
 * All of them can be overridden by using
 * the class [\Alexya\Router\Router](../Alexya/Router/Router.php)
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */

return [
    /**
     * Default route
     */
    "default" => function() {
        \Alexya\Router\Route::route();
    }
];
