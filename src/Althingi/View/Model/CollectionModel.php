<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 9:23 PM
 */

namespace Althingi\View\Model;

use Zend\View\Model\ViewModel;
use \Zend\View\Model\ModelInterface as BaseModelInterface;

class CollectionModel extends ItemModel
{

    /**
     * Set HTTP range header.
     *
     * @param int $lower
     * @param int $upper
     * @param int $size Complete size of collection
     * @return $this
     * @todo implement
     */
    public function setRange($lower, $upper, $size)
    {
        $this->setOption('Range-Unit', "items");
        $this->setOption('Content-Range', "items {$lower}-{$upper}/{$size}");
        return $this;
    }

    /**
     * Constructor
     *
     * @param  null|array|\Traversable $variables
     * @param  array|\Traversable $options
     */
    public function __construct(array $variables = null, $options = null)
    {
        $this->variables = $variables;
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }
}
