<?php
namespace Alexya\Foundation;

use \Exception;

use \Alexya\Container;

use Alexya\Foundation\View\{
    Theme,
    Parser
};

use \Alexya\FileSystem\File;
use \Alexya\Tools\{
    Collection,
    Str
};

/**
 * View class.
 * ===========
 *
 * The view is the file that are going to be rendered and displayed
 * in the browser.
 *
 * You don't need to create a PHP class for each view (although you can),
 * just instance an object of this class and give the path to the file to
 * render as parameter.
 *
 * You can add variables to the view by using the method `\Alexya\Foundation\View::set`
 * or the magic method `\Alexya\Foundation\View::__set`.
 *
 * You can specify the global theme for the view by setting the `$theme` static property.
 * If you don't and there session variable `theme` is set, it will be used to set the theme,
 * being the variable the key of the `application.view.themes` settings array.
 *
 * Example:
 *
 * ```php
 * // Default theme.
 * View::$theme = new DefaultTheme();
 *
 * // Set global $theme through the session variable.
 * Container::Session()->set("theme", "test");
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class View extends Component
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

    /**
     * Global theme.
     *
     * Default theme to use for all views.
     *
     * @var Theme
     */
    public static $theme = null;

    /**
     * Global variables array.
     *
     * Contains the variables that will
     * passed to the renderer and can be
     * accessed to ALL views.
     *
     * @var \Alexya\Tools\Collection
     */
    protected static $_globalData;

    /**
     * Set global method.
     *
     * Adds a variable to the array that can by accessed
     * by all views.
     *
     * @param string $key   Key in `_data` array
     * @param mixed  $value Value for `$key`
     */
    public static function global(string $key, $value)
    {
        if(static::$_globalData === null) {
            static::$_globalData = new Collection();
        }

        static::$_globalData[$key] = $value;
    }

    ///////////////////////////////////////
    // Non static methods and properties //
    ///////////////////////////////////////

    /**
     * View's variables.
     *
     * @var Collection
     */
    protected $_data;

    /**
     * View parsers.
     *
     * @var array
     */
    protected $_parsers = [];

    /**
     * Path to the file to render.
     *
     * @var string
     */
    private $_path = "";

    /**
     * Name of the view to render
     *
     * @var string
     */
    private $_name = "";

    /**
     * Initializes the view.
     */
    protected function _init()
    {
        $this->_data    = new Collection();
        $this->_parsers = Container::Settings()->get("alexya.view.parsers");

        // Override the theme by using the `$_SESSION["theme"]` var.
        $themes = Container::Settings()->get("alexya.view.themes");
        $theme  = Container::Session()->get("theme");
        if(isset($themes[$theme])) {
            self::$theme = $themes[$theme];
        }

        $this->set("theme", self::$theme);

        $this->onInstance();
    }

    /**
     * The onInstance method.
     *
     * It's executed once the view has been instantiated.
     */
    public function onInstance()
    {

    }

    /**
     * Sets view path.
     *
     * @param string $path View path.
     */
    public function setPath(string $path)
    {
        $path = str_replace("\\", DS, $path);

        if(Str::startsWith($path, DS)) {
            $path = substr($path, 1);
        }

        if(!Str::startsWith($path, ROOT_DIR)) {
            $path = ROOT_DIR . $path;
        }

        if(!Str::endsWith($path, DS)) {
            $path .= DS;
        }

        $this->_path = $path;
    }

    /**
     * Sets view name.
     *
     * @param string $name View name.
     */
    public function setName(string $name)
    {
        if(self::$theme instanceof Theme) {
            $name = self::$theme->viewName($name);
        }

        $settings = Container::Settings()->get("alexya.view");

        // Assure that the name is associated with a valid parser
        foreach($this->_parsers as $key => $value) {
            if(Str::endsWith($name, ".{$key}")) {
                $this->_name = $name;

                return;
            }
        }

        // Set name to the default parser
        $this->_name = $name .".". $settings["default"];
    }

    /**
     * Adds (or overrides) a parser.
     *
     * @param string $extension File extension associated with the parser.
     * @param string $parser    Parser class.
     */
    public function parser(string $extension, string $parser)
    {
        $this->_parsers[$extension] = $parser;
    }

    /**
     * Renders the view.
     *
     * @return string Rendered view.
     */
    public function render() : string
    {
        $file = $this->_getFile();

        /**
         * Parser object.
         *
         * @var Parser $parser
         */
        $parser = null;

        $this->_data->append(View::$_globalData);

        foreach($this->_parsers as $extension => $class) {
            if($file->getExtension() === $extension) {
                $parser = new $class($file, $this->_data);

                break;
            }
        }

        if($parser === null) {
            Container::Logger()->debug("Couldn't get parser for view ". $file->getPath());

            return "";
        }

        Container::Logger()->debug("View rendered: ". $file->getPath() ." (". get_class($parser) .")");

        return $parser->parse();
    }

    /**
     * Returns the view file.
     *
     * @return File View file.
     *
     * @throws Exception If the file does not exist.
     */
    protected function _getFile() : File
    {
        $path = $this->_path.$this->_name;
        if(File::exists($path)) {
            return new File($path);
        }

        throw new Exception("The view file {$path} doesn't exist!");
    }

    /**
     * Auto render the view.
     *
     * @return string Rendered view.
     */
    public function __toString() : string
    {
        try {
            return $this->render();
        } catch(Exception $e) {
            return "";
        }
    }

    /**
     * Sets a variable.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     */
    public function set(string $name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * Returns a variable.
     *
     * @param string $name    Variable name.
     * @param mixed  $default Default value to return.
     *
     * @return mixed $value Variable value (or `$default`).
     */
    public function get(string $name, $default = null)
    {
        return ($this->_data[$name] ?? $default);
    }
}
