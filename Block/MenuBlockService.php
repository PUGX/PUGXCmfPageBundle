<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 03/08/15
 * Time: 22:21
 */

namespace PUGX\Cmf\PageBundle\Block;


use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuBlockService extends BaseBlockService
{
    /**
     * @var PhpcrMenuProvider
     */
    private $menuProvider;

    /**
     * @param string $name
     * @param EngineInterface $templating
     * @param PhpcrMenuProvider $menuProvider
     */
    public function __construct($name, EngineInterface $templating, PhpcrMenuProvider $menuProvider)
    {
        parent::__construct($name, $templating);
        $this->menuProvider = $menuProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = array(
            'id' => $this->menuProvider->getMenuRoot(),
            'selected' => '',
        );
        $settings = array_merge($blockContext->getSettings(), $settings);
        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block_context' => $blockContext,
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
            ),
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'url' => false,
                'title' => 'dashboard.menu',
                'label_catalogue' => 'PUGXCmfPageBundle',
                'template' => 'PUGXCmfPageBundle:Block:menu.html.twig',
                'tree_id' => 'menu_admin',
            )
        );
    }
}
