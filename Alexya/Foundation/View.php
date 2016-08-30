<?php
namespace Alexya\Foundation;

use \Exception;

use \Alexya\Container;
use \Alexya\FileSystem\File;
use \Alexya\Tools\{
    Collection,
    Str
};

/**
 * View class.
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
 * @author Manulaiko <manulaiko@gmail.com>
 */
class View extends Component
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

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
        if(static::$_globalData == null) {
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
     * @var \Alexya\Tools\Collection
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
        $this->_data = new Collection();

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

        if(!Str::startsWith($path, ROOT_DIR)) {
            if(Str::startsWith($path, DS)) {
                $path = ROOT_DIR . substr($path, 1);
            } else {
                $path = ROOT_DIR . $path;
            }
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
        $settings = Container::Settings()->get("alexya.view");

        // Assure that the name is associated with a valid parser
        foreach($this->_parsers as $key => $value) {
            if(Str::endsWith($name, ".{$key}")) {
                $this->_name = $name;

                return;
            }
        }

        // Set name to the default parser
        $this->_name = $name .".". $settings["default"];    }

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
        $file   = $this->_getFile();
        $parser = null;
        $this->_data->append(View::$_globalData);

        foreach($this->_parsers as $extension => $class) {
            if($file->getExtension() == $extension) {
                $parser = new $class($file, $this->_data);

                break;
            }
        }

        if($parser == null) {
            Container::Logger()->debug("Couldn't get parser for view ". $file->getPath());

            return "";
        }

        Container::Logger()->debug("View rendered: ". $file->getPath() ." (". get_class($parser) .")");

        return $parser->parse();
    }

    /**
     * Returns the view file.
     *
     * @return \Alexya\FileSystem\File View file.
     */
    protected function _getFile() : File
    {
        if(File::exists($this->_path.$this->_name)) {
            return new File($this->_path.$this->_name);
        }

        throw new Exception("The view file {$this->_path}{$this->_name} doesn't exist!");
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
}
