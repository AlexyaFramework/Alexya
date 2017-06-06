<?php
namespace Alexya;

use \Closure;

/**
 * Container class
 *
 * Implements the Inversion of Control along with the
 * Dependency Injection design pattern.
 * You can use this class for instancing objects with automatic dependency injection.
 *
 * First you need to register the bind. You can do so with the `register` method:
 *
 * ```php
 * Container::register("User", function($name, $password) {
 *     $user = new User($name, $password);
 *
 *     return $user;
 * });
 *```
 *
 * Alternatively, if you want the returned object to be instanced just once,
 * so there's only one instance of the object, use the method `registerSingleton`:
 *
 * ```php
 * Container::registerSingleton("Database", function() {
 *     $settings = [];
 *     $database = new Database($settings);
 *
 *     return $database;
 * });
 * ```
 *
 * Both methods accepts 2 parameters:
 *  * A string being the name to reference the bindnig.
 *  * A closure that will be executed for instancing the object.
 *
 * Once the binding has been registered you can retrieve it using the method `get`:
 *
 * ```php
 * $user     = Container::get("User", ["test", "test"]);
 * $database = Container::get("Database");
 * ```
 *
 * Alternatively you can take advantage of the `__callStatic` method for retrieving bindings:
 *
 * ```php
 * $user = Container::User("test", "test");
 * ```
 *
 * The parameter it accepts is the string sent to the `register` or `registerSingleton` method
 * to identify the binding. You can send an array containing the parameters that will be sent to the closure.
 *
 * To check if a specific binding has been registered use the method `isRegistered` the same
 * way as the `get` method. It will return `true` if the binding is registered or `false` if not.
 *
 * To unregister a binding use the method `unregister` which accepts as parameter the name of the binding.
 *
 * @method static Logger\AbstractLogger   Logger     Returns the logger object.
 * @method static Database\Connection     Database   Returns the Database connection.
 * @method static Settings                Settings   Returns the Settings object.
 * @method static Router\Router           Router     Returns the Router object.
 * @method static Tools\Session\Session   Session    Returns the Session object.
 * @method static Localization\Translator Translator Returns the Translator object.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Container
{
    /**
     * Array containing registered bindings
     *
     * @var array
     */
    private static $_bindings = [];

    /**
     * Checks whether a binding has been registered or not.
     *
     * @param string $name Binding's name.
     *
     * @return bool `true` if `$name` is a registered binding, `false` if not.
     */
    public static function isRegistered(string $name) : bool
    {
        return isset(self::$_bindings[$name]);
    }

    /**
     * Unregisters a binding.
     *
     * @param string $name Binding's name.
     */
    public static function unregister(string $name)
    {
        unset(self::$_bindings[$name]);
    }

    /**
     * Registers a binding.
     *
     * If `$name` is already registered it will be overridden.
     *
     * @param string  $name    Binding's name.
     * @param Closure $closure Closure to execute to instance the binding.
     */
    public static function register(string $name, Closure $closure)
    {
        self::$_bindings[$name] = [
            "type"    => "nonSingleton",
            "closure" => $closure
        ];
    }

    /**
     * Registers a singleton binding.
     *
     * The closure will be executed just once so there's only one instance of the binding.
     *
     * @param string  $name    Binding's name.
     * @param Closure $closure Closure to execute to instance the binding.
     */
    public static function registerSingleton(string $name, Closure $closure)
    {
        self::$_bindings[$name] = [
            "type"       => "singleton",
            "closure"    => $closure,
            "isExecuted" => false
        ];
    }

    /**
     * Returns the binding.
     *
     * @param string $name Binding's name.
     * @param array  $args Arguments sent to the binding.
     *
     * @return mixed The result of calling binding's closure.
     */
    public static function get(string $name, array $args = [])
    {
        if(!self::isRegistered($name)) {
            return null;
        }

        $binding = self::$_bindings[$name];

        if($binding["type"] !== "singleton") {
            return $binding["closure"](... $args);
        }

        $result = $binding["closure"];
        if(!$binding["isExecuted"]) {
            $binding["isExecuted"] = true;
            $binding["closure"]    = $binding["closure"](... $args);

            self::$_bindings[$name] = $binding;

            $result = $binding["closure"];
        }

        return $result;
    }

    /**
     * Alternative syntax to `Container::get($name)`.
     *
     * Example:
     *
     * ```php
     * Container::Router(); // Same as `Container::get("Router")`
     * ```
     *
     * @param string $name Binding's name.
     * @param array  $args Arguments sent to the binding.
     *
     * @return mixed The result of calling binding's closure.
     *
     * @see Container::get
     */
    public static function __callStatic(string $name, array $args)
    {
        return self::get($name, $args);
    }
}
