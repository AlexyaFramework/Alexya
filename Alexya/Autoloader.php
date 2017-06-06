<?php
namespace Alexya;

use Alexya\Tools\Str;

/**
 * Class autoloader.
 *
 * This autoloader allows you to autoload classes.
 *
 * The constructor accepts as parameter the closure that will be called to
 * translate a class name to it's path, if it's not specified the PSR-0 standard
 * will be used.
 *
 * The closure must accept as parameter a string being the requested class and an
 * array being the registered prefixes, it returns the file name of the class.
 *
 * The method `register` registers given namespace in the autoloader.
 * It accepts as parameter the name of the namespace and the path where its classes
 * are located.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Autoloader
{
    /**
     * Closure to translate names to paths.
     *
     * @var callable
     */
    private $_closure = null;

    /**
     * Registered prefixes.
     *
     * @var array
     */
    private $_prefixes = [];

    /**
     * Constructor.
     *
     * @param callable $closure Closure that translates names to paths.
     */
    public function __construct(callable $closure = null)
    {
        if($closure === null) {
            $closure = function($name, $prefixes) {
                foreach($prefixes as $key => $value) {
                    if(!Str::startsWith($name, $key)) {
                        continue;
                    }

                    // Assure that path ends with a directory separator.
                    $value = Str::trailing($value, DS);

                    $class = substr($name, strlen($key));
                    $file  = $value . str_replace("\\", DS, $class) .".php";

                    return $file;
                }

                return str_replace("\\", DS, $name) .".php";
            };
        }

        $this->_closure = $closure;
    }

    /**
     * Adds a new prefix.
     *
     * If `$prefix` is an array, the key will be used as
     * prefix and the value as path.
     *
     * @param string|array $prefix Namespace to autoload.
     * @param string       $path   Path where it's located.
     */
    public function add($prefix, string $path = "")
    {
        if(is_array($prefix)) {
            foreach($prefix as $key => $value) {
                $this->add($key, $value);
            }
        }

        if(Str::startsWith($prefix, "\\")) {
            $prefix = substr($prefix, 1);
        }

        $this->_prefixes[$prefix] = $path;
    }

    /**
     * Performs the autoload.
     *
     * @param string $class Class to autoload.
     *
     * @return bool `true` if the class was successfully loaded, `false` if not.
     */
    public function load(string $class) : bool
    {
        if(Str::startsWith($class, "\\")) {
            $class = substr($class, 1);
        }

        $file = ($this->_closure)($class, $this->_prefixes);
        if(!file_exists($file)) {
            Container::Logger()->error("The file `{$file}` for class `{$class}` does not exist!");

            return false;
        }

        require_once($file);

        return true;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether this instance should be placed at the end of the stack or not.
     */
    public function register(bool $prepend = false)
    {
        spl_autoload_register([$this, "load"], true, $prepend);
    }

    /**
     * Removes this instance from the registered autoloaders.
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, "load"]);
    }
}
