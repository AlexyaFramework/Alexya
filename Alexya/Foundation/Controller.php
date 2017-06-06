<?php
namespace Alexya\Foundation;

use \Alexya\Http\Response;

/**
 * Controller class.
 *
 * This class will handle all actions that the user will request
 * You MUST extend this class in order to use (H)MV(VM)C.
 *
 * In your Controller class, all methods can be routed, meaning
 * that if you have a controller "test" that can be access through
 * the URI "/test/", all methods in "test" controller will be
 * accessible through the URI like this:
 *
 *  - /test/index
 *  - /test/secret_method
 *  - /test/delete_all_accounts_because_of_the_lulz
 *
 * If you don't want an specific method to be routable add it to the
 * array "noRouteable":
 *
 * ```php
 * public function onInstance()
 * {
 *     $this->noRouteable[] = "secret_method";
 *     $this->noRouteable[] = "delete_all_accounts_because_of_the_lulz"
 * }
 * ```
 *
 * Please, DO NOT override the constructor since it will contain needed information
 * to start up the controller, instead use the function `\Alexya\Foundation\Controller::onInstance`
 * that will be executed right after the constructor.
 *
 * Each method that can be routed MUST return an object of type `\Alexya\Http\Response` because
 * it will be the response to send once it's executed.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Controller extends Component
{
    /**
     * Non routeable methods.
     *
     * In this array there will be
     * all methods name that can't be
     * routeable through the request URI.
     *
     * @var array Array containing methods.
     */
    public $noRouteable = [];

    /**
     * Initializes the controller.
     */
    protected function _init()
    {
        $this->noRouteable[] = "onInstance";

        $this->onInstance();
    }

    /**
     * The onInstance method.
     *
     * It's executed once the controller has been instantiated.
     */
    public function onInstance()
    {

    }

    /**
     * Default action.
     *
     * If no action is requested it will execute this.
     *
     * @return \Alexya\Http\Response Response object.
     */
    public function index()
    {
        $response = new Response([
            "Content-Type" => "text/html"
        ], "Hey, it seems we have nothing to show you!");

        return $response;
    }

    /**
     * Checks whether a method is routeable or not.
     *
     * @param string $method Method name.
     *
     * @return bool Whether method is routeable or not.
     */
    public function isRouteable(string $method) : bool
    {
        if(!method_exists($this, $method)) {
            return false;
        }

        foreach($this->noRouteable as $m) {
            if($method === $m) {
                return false;
            }
        }

        return true;
    }

    /**
     * Default internal routing.
     *
     * Routes the request to the controller methods by default, you can override this.
     *
     * @return Response|mixed Response object.
     */
    public function render()
    {
        $response = "";
        $URI = $this->_request->uri();

        if(
            !empty($URI[1]) &&
            $this->isRouteable($URI[1])
        ) {
            $params = [];
            if(isset($URI[2])) {
                $params = array_slice($URI, 2);
            } else if(count($this->_request->post) > 0) {
                $params = $this->_request->post;
            }

            $response = $this->{$URI[1]}(... $params);
        }

        if(!empty($this->_request->post["action"])) {
            $params = [];
            if(isset($URI[2])) {
                $params = array_slice($URI, 2);
            } else if(count($this->_request->post) > 0) {
                $params = $this->_request->post;
            }

            $response = $this->{$URI[1]}(... $params);
        }

        if(empty($response)) {
            return $this->index();
        }

        /*if(!($response instanceof Response)) {
            $response = new Response([
                "Content-Type" => "text/html"
             ], $response);
        }*/

        return $response;
    }
}
