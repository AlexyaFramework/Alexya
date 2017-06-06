<?php
namespace Alexya\Foundation\View;

use Alexya\Tools\Str;

/**
 * Theme class.
 * ============
 *
 * All themes should extend this class.
 *
 * The `$_name` property is the name of the theme, it should be overridden by the
 * child class in order to provide the correct name.
 * It will be used by the `url` method to retrieve the URL to the theme's directory where
 * all its assets will be stored.
 *
 * You can also set the `$URL` static property to provide a base URL.
 * By default it's `/themes/`, meaning that the theme `test` will have its assets in the
 * `/themes/assets/` URL.
 *
 * The method `getView` returns a view object for the current theme, it accepts as parameter the name of
 * the view to return.
 * By default, the views should be named like `name-theme`, for example `index-default.tpl`.
 * You can also set the `$viewFormat` static property to specify the format of the view names.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Theme
{
    ///////////////////////////////////
    // Static methods and properties //
    ///////////////////////////////////

    /**
     * Base URL for all themes.
     *
     * @var string
     */
    public static $URL = "/themes/";

    /**
     * View file name format.
     *
     * You can use the following placeholders:
     *
     *  * `name`: Name of the view.
     *  * `theme`: Theme name.
     *
     * For example:
     *
     * ```
     * Theme::$viewFormat = "{name}-{theme}";
     * ```
     *
     * @var string
     */
    public static $viewFormat = "{name}-{theme}";

    ///////////////////////////////////////
    // Non static methods and properties //
    ///////////////////////////////////////

    /**
     * Theme name.
     *
     * @var string
     */
    protected $_name = "default";

    /**
     * Returns the full URL to the theme directory.
     *
     * @param bool $trailingSlash Whether the return URL should contain a trailing slash or not.
     *
     * @return string URL to theme's directory.
     */
    public function url(bool $trailingSlash = true) : string
    {
        $url = Str::trailing(static::$URL, "/") . Str::trailing($this->_name, "/");

        if(!$trailingSlash) {
            return substr($url, 0, -1);
        }

        return $url;
    }

    /**
     * Returns the view path for the theme.
     *
     * @param string $name View name.
     *
     * @return string View name for the `$name` view.
     */
    public function viewName(string $name) : string
    {
        return Str::placehold(static::$viewFormat, [
            "name"  => $name,
            "theme" => $this->_name
        ]);
    }
}
