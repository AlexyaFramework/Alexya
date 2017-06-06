<?php
/**
 * Alexya framework - The intelligent Loli Framework.
 *
 * This file creates the configuration array and loads
 * all configuration files needed.
 *
 * All of this settings can be overridden once bootstrapping has finished
 * by using the class `\Alexya\Settings`.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */

return [
    /**
     * Default locale.
     *
     * @see \Alexya\Localization\Transaltor For a full list of locale.
     */
    "locale"  => "en",

    /**
     * View settings.
     */
    "view" => [
        /**
         * Default parser.
         */
        "default" => "tpl",

        /**
         * Available parsers.
         *
         * The key of the index is the extension associated to the
         * parser and the value is the name of the parser class.
         */
        "parsers" => [
            "php"  => "\\Alexya\\Foundation\\View\\DefaultParser",
            "html" => "\\Alexya\\Foundation\\View\\HtmlParser",
            "tpl"  => "\\Alexya\\Foundation\\View\\SmartyParser",
        ],

        /**
         * Available themes.
         *
         * The key is the name of the theme (used by the session variable).
         * The value is the `\Alexya\Foundation\View\Theme` instance.
         */
        "themes" => [

        ]
    ],

    /**
     * Logger's settings.
     */
    "logger" => [
        /**
         * Whether logging is enabled or not.
         */
        "enabled" => true,

        /**
         * Where to log (database or file).
         */
        "type" => "file",

        /**
         * Log levels that should log.
         */
        "log_levels" => [
           "emergency" => true,
           "alert"     => true,
           "critical"  => true,
           "error"     => true,
           "warning"   => true,
           "notice"    => true,
           "info"      => true,
           "debug"     => true
       ],

       /**
        * File logger settings.
        */
       "file" => [
           /**
            * Log file format.
            */
           "file_name" => "{YEAR}-{MONTH}-{DAY}.log",

           /**
            * Log message format.
            */
           "log_format" => "[{HOUR}:{MINUTE}] ({LEVEL}) {LOG}",

           /**
            * Log directory.
            */
           "directory" => ROOT_DIR."logs".DS
       ],

       /**
        * Database logger settings.
        */
       "database" => [
           /**
            * Table name.
            */
           "table" => "logs",

           /**
            * Table columns.
            */
           "columns" =>  [
               "date"    => "{YEAR}-{MONTH}-{DAY} {HOUR}:{MINUTE}:{SECOND}",
               "caller"  => "{CALLER_CLASS}{CALLER_TYPE}{CALLER_FUNCTION} ({CALLER_FILE}:{CALLER_LINE})",
               "level"   => "{LEVEL}",
               "message" => "{LOG}"
           ]
       ]
    ],

    /**
     * Upload settings.
     */
    "uploads" => [
        /**
         * Whether file uploading is enabled or not.
         */
        "enabled" => true,

        /**
         * Allowed file extensions that can be uploaded.
         *
         * Each index contains as key the extension name (it can be a regexp)
         * and the value is the absolute path to the directory on which the file will be saved.
         *
         * If any file does not match the regexp it won't be saved.
         *
         * This settings can be overridden, for more info go to [\Alexya\Upload::save](../Alexya/Upload).
         */
        "directories" => [
            "*" => ROOT_DIR."uploads"
        ]
    ],

    /**
     * Cache settings.
     */
    "cache" => [
        /**
         * Whether the caching system is enabled or not.
         */
        "enabled"  => true,

        /**
         * Lifetime of the cache (in seconds).
         */
        "lifetime" => 21600
    ],

    /**
     * Session settings.
     */
    "session" => [
        /**
         * Whether the session is enabled or not.
         */
        "enabled" => true,

        /**
         * Session name.
         */
        "name"    => "Alexya",

        /**
         * Session save path.
         */
        "path" => ROOT_DIR."sessions",

        /**
         * Session's lifetime (in seconds).
         */
        'lifetime' => 7200
    ],

    /**
     * SocksWork settings.
     */
    "sockswork" => [
        /**
         * Whether SocksWork is enabled or not.
         */
        "enabled" => false,

        /**
         * Timeout in milliseconds for connection.
         */
        "timeout" => 100,

        /**
         * IP/Host to connect.
         */
        "server"  => "127.0.0.1",

        /**
         * Server's port.
         */
        "port"    => 1207
    ],

    /**
     * Router settings.
     */
    "router" => [
        /**
         * Router's base path.
         */
        "base_path" => "",

        /**
         * URI used to match routes.
         */
        "uri" => ($_SERVER["PATH_INFO"] ?? "/")
    ]
];
