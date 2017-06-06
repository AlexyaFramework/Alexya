<?php
/**
 * Alexya Framework - The intelligent Loli Framework.
 *
 * This file bootstraps the application.
 *
 * It will load composer's autoloader, configuration files
 * and register `\Alexya\Container` bindings.
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
        return null;
    }

    if($settings["type"] === "database") {
        $database = \Alexya\Container::Database();

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
    $Settings = \Alexya\Container::Settings();

    $routes = require_once(ROOT_DIR."config".DS."routes.php");
    $router = new \Alexya\Router\Router(
        $Settings->get("alexya.router.base_path"),
        $Settings->get("alexya.router.uri")
    );

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
if(
    \Alexya\Container::Settings()->get("database.enabled") &&
    class_exists("\\Alexya\\Database\\Connection")
) {
    \Alexya\Container::registerSingleton("Database", function() {
        $settings = \Alexya\Container::Settings()->get("database");

        $database = new \Alexya\Database\Connection(
            $settings["host"],
            $settings["port"],
            $settings["username"],
            $settings["password"],
            $settings["database"]
        );

        return $database;
    });

    // Initialize ORM model
    \Alexya\Database\ORM\Model::initialize(
        \Alexya\Container::Database(),
        \Alexya\Container::Settings()->get("database.namespace")
    );
}

// And for Session
if(
    \Alexya\Container::Settings()->get("alexya.session.enabled") &&
    class_exists("\\Alexya\\Tools\\Session\\Session")
) {
    \Alexya\Container::registerSingleton("Session", function() {
        $settings = \Alexya\Container::Settings()->get("alexya.session");

        $session = new \Alexya\Tools\Session\Session(
            $settings["name"],
            $settings["lifetime"],
            $settings["path"]
        );

        return $session;
    });
    \Alexya\Tools\Session\Results::initialize(\Alexya\Container::Session());
}
///////////////////////////////////////
// End Register container's bindings //
///////////////////////////////////////

if(class_exists("\\Alexya\\Localization\\Translator")) {
    // Register translator on container
    \Alexya\Container::registerSingleton("Translator", function() {
        $translations = [];
        $directory    = \Alexya\FileSystem\Directory::make(TRANSLATIONS_DIR, \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);
        $loc          = \Alexya\Container::Settings()->get("alexya.locale");

        // All files from the `translations` directory are supposed to be PHP scripts
        // that returns an array with the translations, the name of each file is
        // is the language name used by the translator.
        foreach($directory->getFiles() as $file) {
            $translations[$file->getName()] = require($file->getPath());
        }

        return new \Alexya\Localization\Translator($translations, \Alexya\Localization\Locale::get($loc));
    });

    ////////////////////////
    // Function shortcuts //
    ////////////////////////
    /**
     * @see \Alexya\Localization\Translator::translate
     */
    function t()
    {
        return \Alexya\Container::Translator()->translate(... func_get_args());
    }

    // /**
    //  * @see \Alexya\Localization\Localization::formatNumber
    //  */
    // function fNumber()
    // {
    //     return \Alexya\Localization\Localization::formatNumber(...func_get_args());
    // }
    //
    // /**
    //  * @see \Alexya\Localization\Localization::formatDate
    //  */
    // function fDate()
    // {
    //     return \Alexya\Localization\Localization::formatDate(...func_get_args());
    // }
}

\Alexya\Container::Logger()->debug("Alexya is bootstrapped!");
