<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 9:22 PM
 */

namespace Althingi\View\Model;

use Traversable;
use \Zend\View\Model\ModelInterface as BaseModelInterface;

class ItemModel extends EmptyModel
{
    /**
     * Constructor
     *
     * @param  null|\stdClass| $variables
     * @param  array|\Traversable $options
     */
    public function __construct(\stdClass $variables = null, $options = null)
    {
        $this->variables = $variables;
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }
}
