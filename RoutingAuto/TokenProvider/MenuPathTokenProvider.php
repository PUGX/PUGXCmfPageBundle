<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/10/15
 * Time: 01:23
 */

namespace PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider;


use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Bundle\CoreBundle\Slugifier\SlugifierInterface;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Component\RoutingAuto\TokenProviderInterface;
use Symfony\Cmf\Component\RoutingAuto\UriContext;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuPathTokenProvider implements TokenProviderInterface
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    private $locale;

    /**
     * MenuPathTokenProvider constructor.
     * @param SlugifierInterface $slugifier
     * @param DocumentManager $documentManager
     */
    public function __construct(SlugifierInterface $slugifier, DocumentManager $documentManager)
    {
        $this->slugifier = $slugifier;
        $this->documentManager = $documentManager;
    }

    /**
     * Return a token value for the given configuration and
     * document.
     *
     * @param UriContext $uriContext
     * @param array $options
     * @return string
     */
    public function provideValue(UriContext $uriContext, $options)
    {
        $path = array();
        $subject = $uriContext->getSubjectObject();

        if ($this->documentManager->isDocumentTranslatable($subject)) {
            $this->locale = $subject->getLocale();
        }

        $this->traversePath($subject, $path);
        if (empty($path)) {
            return '';
        }
        $path = array_map(
            function ($item) {
                return $this->slugifier->slugify($item);
            },
            $path
        );
        $path = array_reverse($path);
        return implode('/', $path);
    }

    /**
     * Configure the options for this token provider
     *
     * @param OptionsResolverInterface $optionsResolver
     */
    public function configureOptions(OptionsResolverInterface $optionsResolver)
    {
    }

    /**
     * @param $subject
     * @param $path
     */
    private function traversePath($subject, array &$path)
    {
        if (!$subject instanceof RouteTokenProviderInterface) {
            return;
        }

        $path[] = $subject->provideRouteToken();

        if (!$subject instanceof PrimaryMenuNodeProviderInterface) {
            return;
        }

        $menuNode = $subject->providePrimaryMenuNode();
        if (!$menuNode instanceof MenuNode) {
            return;
        }

        $parentNode = $menuNode->getParentObject();
        if (!$parentNode instanceof MenuNode) {
            return;
        }

        $parentSubject = $parentNode->getContent();
        if ($this->locale && $this->documentManager->isDocumentTranslatable($parentSubject)) {
            $meta = $this->documentManager->getMetadataFactory()->getMetadataFor(get_class($parentSubject));
            $parentSubject = $this
                ->documentManager
                ->findTranslation($meta->getName(), $meta->getIdentifierValue($parentSubject), $this->locale);
        }

        $this->traversePath($parentSubject, $path);
    }
}
