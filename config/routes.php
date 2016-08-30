<?php
/**
 * Alexya Framework - The intelligent Loli Framework
 *
 * This file contains the default routes.
 * All of them can be overridden by using
 * the class `\Alexya\Router\Router`
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */

return [
    /**
     * Default route.
     *
     * Performs the routing based on the request URI.
     *
     * It goes as follow:
     *
     *     /Controller/Action
     *
     * If `Action` is empty the default action `index` will be called on `Controller`,
     * this means that `/Login/index` is the same as `/Login`.
     *
     * If the controller for the request doesn't exist or the action can't be
     * executed an exception will be thrown.
     *
     * The controller action can return a string being the response content,
     * how ever, it's recommended that it returns an instance of the `\Alexya\Http\Response`
     * object to send.
     *
     * Finally, the response is sent and the script finish the execution.
     */
    "{DEFAULT}" => function() {
        $request = \Alexya\Http\Request::main();
        $uri     = $request->uri();

        $page    = $uri[1];
        $action  = ($uri[2] ?? ($_POST["action"] ?? "index"));
        $params  = array_slice($uri, 3);

        $triad = new \Alexya\Foundation\Page($page, $request);

        if(
            !is_callable([$triad->Controller, $action]) ||
            empty($page)
        ) {
            // The requested action isn't available, throw exception
            throw new Exception("The requested action couldn't be performed!");
        }

        if(!empty($_POST)) {
            unset($_POST["action"]);
            $params = $_POST;
        }

        $response = $triad->Controller->$action(... $params);

        if(!$response instanceof \Alexya\Http\Response) {
            // Allow user to return the content of the response instead of the
            // whole response object
            $response = new \Alexya\Http\Response([
                "Content-Type" => "text/html"
            ], $response);
        }

        $response->send();
        die();
    }
];
