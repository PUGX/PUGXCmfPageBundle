<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 04/06/15
 * Time: 08:36.
 */

namespace PUGX\Cmf\PageBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

/**
 * Class MenuNode.
 *
 * @PHPCR\Document(referenceable=true)
 */
class MenuNode extends \Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode
{
    const PATH_SEPARATOR = ' > ';

    public function getParentPathLabel()
    {
        return implode(self::PATH_SEPARATOR, $this->getParentPathComponents());
    }

    public function __toString()
    {
        return implode(self::PATH_SEPARATOR, array_merge($this->getParentPathComponents(), array($this->getLabel())));
    }

    /**
     * @PHPCR\PrePersist
     */
    public function generateUniqidAsName()
    {
        if ($this->getName()) {
            return;
        }
        $this->setName(uniqid());
    }

    /**
     * @return array
     */
    private function getParentPathComponents()
    {
        $pathLabels = array();
        $parent = $this->getParentDocument();
        while (true) {
            $pathLabels[] = $parent->getLabel();
            if ($parent instanceof Menu) {
                break;
            }
            $parent = $parent->getParentDocument();
        }
        $pathLabels = array_reverse($pathLabels);

        return $pathLabels;
    }
}
