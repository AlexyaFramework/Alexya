<?php
namespace Alexya;

/**
 * Session class.
 *
 * This class handles the session.
 *
 * The constructor accepts as parameter the session name, the lifetime of the cookie
 * and the path were it should be stored.
 *
 * Once the object is instantiated the session will be started, to stop it use the method `stop`.
 *
 * Method summary:
 *
 *  * `get`: Returns given variable.
 *  * `__get`: Alias of `get`.
 *  * `set`: Sets given variable.
 *  * `__set`: Alias of `set`.
 *  * `exists`: Checks whether a variable exists on the session.
 *  * `__isset`: Alias of `exists`.
 *  * `remove`: Deletes given variable.
 *  * `__unset`: Alias of `remove`
 *
 * Use the magic methods `__get` and `__set` to get and set session variables.
 * To remove a variable use the method `remove` or `__unset`.
 * To check if a variable exists use the method `exists` or `__isset`.
 *
 * Example:
 *
 *     $Session = new \Alexya\Session("alexya", (3600 * 24), "sessions");
 *     $Session->foo = "bar";
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Session
{
    /**
     * Constructor.
     *
     * @param string $name     Session name.
     * @param int    $lifetime Cookie's lifetime.
     * @param string $path     Save path.
     */
    public function __construct(string $name, int $lifetime, string $path)
    {
        //Start session
        ini_set("session.name", $name);
        ini_set("session.lifetime", $lifetime);
        ini_set("session.save_path", $path);

        session_start();
    }

    /**
     * Checks if variable exists in session.
     *
     * @param string $name Variable name.
     *
     * @return bool `true` if `$name` exists as a session variable, `false` if not
     */
    public function exists(string $name) : bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Removes a variable from the session.
     *
     * @param string $name Variable name.
     */
    public function remove(string $name)
    {
        if(!$this->exists($name)) {
            return;
        }

        session_unset($name);
        unset($_SESSION[$name]);
    }

    /**
     * Sets a variable.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Returns a variable.
     *
     * @param string $name Variable name.
     *
     * @return mixed Variable's value.
     */
    public function get(string $name)
    {
        if(!$this->exists($name)) {
            return;
        }

        return $_SESSION[$name];
    }

    /**
     * Stops session.
     */
    public function stop()
    {
        session_destroy();
    }

    /////////////////////////
    // Start magic methods //
    /////////////////////////
    /**
     * Checks if variable exists in session.
     *
     * @param string $name Variable name.
     *
     * @return bool `true` if `$name` exists as a session variable, `false` if not
     */
    public function __isset(string $name) : bool
    {
        return $this->exists($name);
    }

    /**
     * Removes a variable from the session.
     *
     * @param string $name Variable name.
     */
    public function __isset(string $name)
    {
        return $this->remove($name);
    }

    /**
     * Sets a variable.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     */
    public function __set(string $name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Returns a variable.
     *
     * @param string $name Variable name.
     *
     * @return mixed Variable's value.
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
    ///////////////////////
    // End magic methods //
    ///////////////////////
}
