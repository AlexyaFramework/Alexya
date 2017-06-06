<?php
namespace Alexya\Foundation;

use \Alexya\Tools\Collection;

/**
 * Model class.
 * ============
 *
 * This class represents the Model component of the HMV(VM)C.
 *
 * Its propose is to contain the data that's going to be rendered in the View.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Model extends Component
{
    /**
     * Model data.
     *
     * @var Collection
     */
    protected $_data;

    /**
     * Initializes the model.
     */
    protected function _init() : void
    {
        $this->_data = new Collection();

        $this->onInstance();
    }

    /**
     * The onInstance method.
     *
     * It's executed once the model has been instantiated.
     */
    public function onInstance() : void
    {

    }

    /////////////////////////
    // Start magic methods //
    /////////////////////////
    /**
     * Magic method `__set`.
     *
     * Sets dynamically variables.
     *
     * @param string $key   Key in `_data` array.
     * @param mixed  $value Value for `$key`.
     */
    public function __set(string $key, $value) : void
    {
        $this->set($key, $value);
    }

    /**
     * Magic method `__get`.
     *
     * Get dynamically variables.
     *
     * @param string $key Key in `_data` array.
     *
     * @return mixed Value of `$key` (or `null`).
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Magic method `__isset`.
     *
     * Checks whether a variable is set in the
     * array or not.
     *
     * @param string $key Key in `_data` array.
     *
     * @return bool Whether `$key` exists in the array.
     */
    public function __isset(string $key) : bool
    {
        return isset($this->_data->$key);
    }

    /**
     * Magic method `__unset`.
     *
     * Unsets a variable from the array.
     *
     * @param string $key Key in `_data` array.
     */
    public function __unset(string $key) : void
    {
        unset($this->_data->$key);
    }
    ///////////////////////
    // End magic methods //
    ///////////////////////

    /**
     * Get method.
     *
     * Retrieves a variable from the array.
     *
     * @param string $key     Key in `_data` array.
     * @param mixed  $default The default value if `$key` des not exists.
     *
     * @return mixed Value of `$key` (or $default).
     */
    public function get(string $key, $default = null)
    {
        return $this->_data->get($key, $default);
    }

    /**
     * Set method.
     *
     * Adds a variable to the array.
     *
     * @param string $key   Key in `_data` array.
     * @param mixed  $value Value for `$key`.
     */
    public function set(string $key, $value) : void
    {
        $this->_data->set($key, $value);
    }

    /**
     * Returns all variables.
     *
     * @return iterable All variables from the array.
     */
    public function all() : iterable
    {
        return $this->_data->getAll();
    }
}
