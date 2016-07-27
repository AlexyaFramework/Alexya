<?php
namespace Alexya;

use \Closure;

/**
 * Container class
 *
 * Implements the Inversion of Control along with the
 * Dependency Injection desing pattern.
 *
 * You can use this class for insancing objects with automatic dependency injection.
 *
 * First you need to register the bind. You can do so with the `register` method:
 *
 *     Container::register("User", function($name, $password) {
 *         $user = new User($name, $password);
 *
 *         return $user;
 *     });
 *
 * Alternatively, if you want the returned object to be instanced just once,
 * so there's only one instance of the object, use the method `registerSingleton`:
 *
 *     Container::registerSingleton("Database", function() {
 *     	   $settings = [];
 *         $database = new Database($settings);
 *
 *         return $database;
 *     });
 *
 * Both methods accepts 2 parameters:
 *  * A string being the name to reference the bindnig.
 *  * A closure that will be executed for instancing the object.
 *
 * The difference between both methods (a part that one executes the closure just once) is that
 * the closure sent to `register` can contain parameters but `registerSingleton` can't.
 *
 * Once the binding has been registered you can retrieve it using the method `get`:
 *
 *     $user     = Container::get("User", ["test", "test"]);
 *     $database = Container::get("Database");
 *
 * Alternatively you can take advantage of the `__callStatic` method for retrieving bindings:
 *
 *     $user = Container::User("test", "test");
 *
 * The parameter it accepts is the string sent to the `register` or `registerSingleton` method
 * to identify the binding. If the binding was register with `register` you can send an array containing
 * the parameters that will be sent to the closure.
 *
 * To check if a specific binding has been registered use the method `isRegistered` the same
 * way as the `get` method. It will return `true` if the binding is registered or `false` if not.
 *
 * To unregister a binding use the method `unregister` which accepts as parameter the name of the binding.
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
     * Checks wether a binding has been registered or not.
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
        unset(self::$_bindings);
    }

    /**
     * Registers a binding.
     *
     * If `$name` is already registered it will be overriden.
     *
     * @param string  $name    Binding's name.
     * @param Closure $closure Closure to execute to instance the binding.
     */
    public static function register(string $name, Closure $closure)
    {
        self::$_bindings[$name] = $closure;
    }

    /**
     * Registers a singleton binding.
     *
     * The closure will be executed here so there's only one instance of the binding.
     *
     * @param string  $name    Binding's name.
     * @param Closure $closure Closure to execute to instance the binding.
     */
    public static function registerSingleton(string $name, Closure $closure)
    {
        self::$_bindings[$name] = $closure();
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

        $ret = self::$_bindings[$name];
        if($ret instanceof Closure) {
            $ret = $ret(... $args);
        }

        return $ret;
    }

    /**
     * Alternative syntax to `Container::get($name)`.
     *
     * @param string $name Binding's name.
     * @param array  $args Arguments sent to the binding.
     *
     * @return mixed The result of calling binding's closure.
     *
     * @see \Alexya\Container::get
     */
    public static function __callStatic(string $name, array $args)
    {
        return self::get($name, $args);
    }
}
