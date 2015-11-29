<?php


namespace PUGX\Cmf\PageBundle\Document\Repository;


use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\Id\RepositoryIdInterface;

class PageRepository extends DocumentRepository implements RepositoryIdInterface
{
    const PAGES_PHPCR_BASE_PATH = '/cms/content/page';

    /**
     * Generate a document id
     *
     * @param object $document
     * @param object $parent
     *
     * @return string the id for this document
     */
    public function generateId($document, $parent = null)
    {
        // TODO The pages base path should come from a config parameter.
        return self::PAGES_PHPCR_BASE_PATH . '/' . uniqid();
    }
}
