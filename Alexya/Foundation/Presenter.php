<?php
namespace Alexya\Foundation;

use \Exception;

/**
 * The presenter.
 *
 * The presenter is an intermediary between the Model and
 * the View (it's also called ViewModel).
 *
 * It contains the logic needed to generate the view.
 *
 * The method `\Alexya\Foundation\Presenter::onInstance` is executed once the
 * presenter is instanced and it should contain the logic needed
 * to generate the view.
 *
 * You can add variables to the view the same way as if you instance
 * a `\Alexya\Foundation\View` object.
 *
 * You can also change the view object by using the method `\Alexya\Foundation\Presenter::setView`.
 *
 * And once you're done with the presenter you can call the method
 * `\Alexya\Foundation\Presenter::render` to render the view.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Presenter extends Component
{
    /**
     * Initializes the presenter.
     */
    protected function _init()
    {
        $this->onInstance();
    }

    /**
     * Set's the view logic.
     */
    public function onInstance()
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
    public function __set(string $key, $value)
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
     * Checks whetheer a variable is set in the
     * array or not.
     *
     * @param string $key Key in `_data` array.
     *
     * @return bool Whether `$key` exists in the array.
     */
    public function __isset(string $key) : bool
    {
        return isset($this->triad->View->{$key});
    }

    /**
     * Magic method `__unset`.
     *
     * Unsets a variable from the array.
     *
     * @param string $key Key in `_data` array.
     */
    public function __unset(string $key)
    {
        unset($this->triad->View->{$key});
    }

    /**
     * Auto render the view.
     *
     * @return string Redered view.
     */
    public function __toString() : string
    {
        try {
            return $this->render();
        } catch(Exception $e) {
            return "";
        }
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
        return $this->_triad->View->get($key, $default);
    }

    /**
     * Set method.
     *
     * Adds a variable to the array.
     *
     * @param string $key   Key in `_data` array.
     * @param mixed  $value Value for `$key`.
     */
    public function set(string $key, $value)
    {
        $this->_triad->View->set($key, $value);
    }

    /**
     * Renders the view.
     *
     * @return string Rendered view.
     */
    public function render() : string
    {
        return $this->_triad->View->render();
    }
}
