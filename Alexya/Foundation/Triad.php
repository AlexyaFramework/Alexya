<?php
namespace Alexya\Foundation;

use \Exception;

use \Alexya\Container;
use \Alexya\Http\Request;
use \Alexya\Tools\Collection;

/**
 * Triad class.
 *
 * This class is the main component of the HMV(VM)C design pattern.
 * It's bassically a nexe between all the components of the design pattern:
 *
 *  * The Model
 *  * The View
 *  * The Presenter (ViewModel)
 *  * The Controller
 *
 * By using this class for linking all components we're allowed to introduce a
 * hierarchical view of the design pattern where each triad can have one
 * or more children triads and one parent triad.
 *
 * The constructor accepts as parameter a string being the triad name
 * and an `\Alexya\Http\Request` that represents the request that leaded
 * to the instantiation of the triad.
 *
 * The triad name will be used to instance all of the components since
 * all of them should be on their correspondent package:
 *
 *     $triad = new \Alexya\Foundation\Triad("Test", $request);
 *
 *     var_dump($triad->Model);
 *     // Object of type `\Application\Model\Test`
 *
 *     var_dump($triad->View);
 *     // Object of type `\Application\View\Test`
 *
 *     var_dump($triad->Presenter);
 *     // Object of type `\Application\Presenter\Test`
 *
 *     var_dump($triad->Controller);
 *     // Object of type `\Application\Controller\Test`
 *
 * The property `$_preffix` holds the preffix to apply to the location of the component.
 * By default it's `\Application\`, if however you want to store the components in the
 * package `\Application\Components` extend this class and change the property:
 *
 *     class Triad extends \Alexya\Foundation\Triad
 *     {
 *         protected $_preffix = "\\Application\\Components\\";
 *     }
 *
 *     $triad = new Triad("Test", $request);
 *
 *     var_dump($triad->Model);
 *     // Object of type `\Application\Components\Model\Test`
 *
 *     var_dump($triad->View);
 *     // Object of type `\Application\Components\View\Test`
 *
 *     var_dump($triad->Presenter);
 *     // Object of type `\Application\Components\Presenter\Test`
 *
 *     var_dump($triad->Controller);
 *     // Object of type `\Application\Components\Controller\Test`
 *
 * The components of each triad follows the lazy-load design pattern for an improved
 * performance.
 *
 * For introducing the hierarchical view of the design pattern there are two properties:
 *
 *  * `$children`: A `\Alexya\Tools\Collection` with children triads.
 *  * `$parent`: The parent `\Alexya\Foundation\Triad`.
 *
 * The triad instance can be accessed from any of the components with the `$_triad` property.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Triad
{
    /**
     * Package preffix.
     *
     * @var string
     */
    protected $_preffix = "\\Application\\";

    /**
     * The request that leaded to the instantiation of the triad.
     *
     * @var \Alexya\Http\Request
     */
    protected $_request;

    /**
     * Triad name.
     *
     * @var string
     */
    protected $_name = "";

    /**
     * Children triads.
     *
     * @var \Alexya\Tools\Collection
     */
    public $children;

    /**
     * Parent triad.
     *
     * @var \Alexya\Foundation\Triad
     */
    public $parent;

    /**
     * Components array.
     *
     * @var array
     */
    protected $_components = [];

    /**
     * Constructor.
     *
     * @param string               $name    Triad name.
     * @param \Alexya\Http\Request $request Request.
     */
    public function __construct(string $name, Request $request)
    {
        $this->_name    = $name;
        $this->_request = $request;

        $this->children    = new Collection();
        $this->_components = [
            "view" => [
                "component" => null,
                "name"      => "View\\{$name}\\index"
            ],
            "controller" => [
                "component" => null,
                "name"      => "Controller\\{$name}"
            ],
            "model" => [
                "component" => null,
                "name"      => "Model\\{$name}"
            ],
            "presenter" => [
                "component" => null,
                "name"      => "Presenter\\{$name}"
            ]
        ];
    }

    /**
     * Lazy-loads the components of the triad.
     *
     * All the components name are case insensitive.
     *
     * @param string $name Name of the component.
     *
     * @return mixed Requested component.
     */
    public function __get(string $name)
    {
        $name = strtolower($name);

        if($name == "controller") {
            return $this->_loadController();
        } else if($name == "presenter") {
            return $this->_loadPresenter();
        } else if($name == "model") {
            return $this->_loadModel();
        } else if($name == "view") {
            return $this->_loadView();
        }
    }

    /**
     * Loads and returns the controller.
     *
     * @return \Alexya\Foundation\Controller The controller.
     */
    protected function _loadController() : Controller
    {
        $controller = $this->_components["controller"];

        if($controller["component"] != null) {
            return $controller["component"];
        }

        $class = $this->_preffix . $controller["name"];
        $controller = new $class($this, $this->_request);

        $this->_components["controller"]["component"] = $controller;

        return $controller;
    }

    /**
     * Loads and returns the model.
     *
     * @return \Alexya\Foundation\Model The model.
     */
    protected function _loadModel() : Model
    {
        $model = $this->_components["model"];

        if($model["component"] != null) {
            return $model["component"];
        }

        $class = $this->_preffix . $model["name"];
        $model = new $class($this, $this->_request);

        $this->_components["model"]["component"] = $model;

        return $model;
    }

    /**
     * Loads and returns the view.
     *
     * @return \Alexya\Foundation\View The view.
     */
    protected function _loadView() : View
    {
        $settings = Container::Settings()->get("alexya.view");
        $view     = $this->_components["view"];

        if($view["component"] != null) {
            return $view["component"];
        }

        $view = new View($this, $this->_request);

        foreach($settings["parsers"] as $extension => $class) {
            $view->parser($extension, $class);
        }
        $view->setPath("{$this->_preffix}View\\{$this->_name}");
        $view->setName("index.". $settings["default"]);

        $this->_components["view"]["component"] = $view;

        return $view;
    }

    /**
     * Loads and returns the presenter.
     *
     * @return \Alexya\Foundation\Presenter The presenter.
     */
    protected function _loadPresenter() : Presenter
    {
        $presenter = $this->_components["presenter"];

        if($presenter["component"] != null) {
            return $presenter["component"];
        }

        $class = $this->_preffix ."Presenter\\". $this->_name;

        if(
            !empty($presenter["name"]) &&
            class_exists($presenter["name"])
        ) {
            $class = $presenter["name"];
        }

        $presenter = new $class($this, $this->_request);

        $this->_components["presenter"]["component"] = $presenter;

        return $presenter;
    }
}
