<?php
/**
 * Alexya Framework - The intelligent Loli Framework
 *
 * This file bootstraps the application.
 *
 * It will load composer's autoloader, configuration files
 * and register [Container's](Alexya/Container.php) bindings
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */

/**
 * Load path constants
 */
require_once("config/paths.php");

/////////////////////////////////////////
// Start Register container's bindings //
/////////////////////////////////////////
\Alexya\Container::registerSingleton("Settings", function() {
    $settings = new \Alexya\Settings([
        "alexya"      => require_once(ROOT_DIR."config".DS."alexya.php"),
        "application" => require_once(ROOT_DIR."config".DS."application.php"),
        "database"    => require_once(ROOT_DIR."config".DS."database.php")
    ]);

    return $settings;
});

\Alexya\Container::registerSingleton("Logger", function() {
    $settings = \Alexya\Container::Settings()->get("alexya.logger");

    if(!$settings["enabled"]) {
        return;
    }

    if($settings["type"] == "database") {
        $database = \Alexya\Container::Database;

        $logger = new \Alexya\Logger\Database(
            $database,
            $settings["database"]["table"],
            $settings["database"]["columns"],
            $settings["log_levels"]
        );
    } else {
        $directory = \Alexya\FileSystem\Directory::make($settings["file"]["directory"], \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);

        $logger = new \Alexya\Logger\File(
            $directory,
            $settings["file"]["file_name"],
            $settings["file"]["log_format"],
            $settings["log_levels"]
        );
    }

    return $logger;
});

\Alexya\Container::registerSingleton("Router", function() {
    $routes = require_once(ROOT_DIR."config".DS."routes.php");
    $router = new \Alexya\Router\Router();

    foreach($routes as $key => $value) {
        $method = null;
        if(is_array($value)) {
            $method = $value["method"];
            $value  = $value["route"];
        }

        $router->add($key, $value, $method);
    }

    return $router;
});

// User might not have included the Database components, so just register its binding in case it's included
if(class_exists("\Alexya\Database\Connection") && \Alexya\Container::Settings()->get("database.enabled")) {
    \Alexya\Container::registerSingleton("Database", function() {
        $settings = \Alexya\Container::Settings()->get("database");

        $database = new \Alexya\Database\Connection($settings["host"], $settings["port"], $settings["username"], $settings["password"], $settings["database"]);

        return $database;
    });
}

// Same goes for SocksWork
if(class_exists("\Alexya\SocksWork\Connection")) {
    \Alexya\Container::register("SocksWork", function() {
        $settings = \Alexya\Container::get("Settings");

        $sockswork = new \Alexya\SocksWork\Connection($settings->get("alexya.sockswork"));

        return $sockswork;
    });
}
///////////////////////////////////////
// End Register container's bindings //
///////////////////////////////////////

if(class_exists("\Alexya\Locale\Localization")) {
    ////////////////////////
    // Function shortcuts //
    ////////////////////////
    /**
     * @see \Alexya\Locale\Localization::translate
     */
    function t()
    {
        return \Alexya\Locale\Localization::translate(...func_get_args());
    }

    /**
     * @see \Alexya\Locale\Localization::formatNumber
     */
    function fNumber()
    {
        return \Alexya\Locale\Localization::formatNumber(...func_get_args());
    }

    /**
     * @see \Alexya\Locale\Localization::formatDate
     */
    function fDate()
    {
        return \Alexya\Locale\Localization::formatDate(...func_get_args());
    }
}

// Initialize classes
//\Alexya\Exception\Handler::init();
//\Alexya\Container::get("Router")->init();

\Alexya\Container::Logger()->debug("Alexya is bootstrapped!");
