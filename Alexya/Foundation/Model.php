<?php
namespace Alexya\Foundation;

use \Alexya\Tools\Collection;

/**
 * Model class.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Model extends Component
{
    /**
     * Model data.
     *
     * @var \Alexya\Tools\Collection
     */
    public $data;

    /**
     * Initializes the model.
     */
    protected function _init()
    {
        $this->data = new Collection();

        $this->onInstance();
    }

    /**
     * The onInstance method.
     *
     * It's executed once the model has been instantiated.
     */
    public function onInstance()
    {

    }
}
