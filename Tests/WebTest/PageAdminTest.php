<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 11/07/15
 * Time: 14:28
 */

namespace PUGX\Cmf\Tests\WebTest;


use PUGX\Cmf\PageBundle\Test\IsolatedTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\DomCrawler\Form;

class PageAdminTest extends IsolatedTestCase
{
    public function testCreatePage()
    {
        $client = static::createClient();
        $client->request('GET', '/my-new-page');
        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->createPage($client, 'My New Page', 'Lorem ipsum dolor', 'my-new-page');
        $this->goToPageListAndAssertData($client, array(array('My New Page', '', '/my-new-page')));

        $crawler = $client->request('GET', '/my-new-page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('My New Page', trim($crawler->filter('h2')->text()));
        $this->assertEquals('Lorem ipsum dolor', trim($crawler->filter('p')->text()));
        $this->assertSeoTitleEquals('My New Page - PUGX Cmf Page Bundle', $crawler);
        $this->assertSeoDescriptionEquals(
            'Symonfy2 bundle that provides pages content management system for Symfony2 built on top of Symfony CMF ' .
            'and Sonata Admin bundle.',
            $crawler
        );
        $this->assertSeoKeywordsEquals('symfony2, cmf, sonata, pugx', $crawler);
    }

    public function testEditPage()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest\TestEditPageFixture')
        );

        $client = static::createClient();
        $this->goToPageListAndAssertData($client, array(array('To be edited', '', '/to-be-edited')));

        $this->updatePage(
            $client,
            '/cms/content',
            'to-be-edited',
            array('page[title]' => 'Now it\'s changed!'),
            'now-it-s-changed',
            'Now it\'s changed!'
        );

        $this->goToPageListAndAssertData($client, array(array('Now it\'s changed!', '', '/now-it-s-changed')));
    }

    public function testEditPageWithoutChangingTheTitleShouldNotChangeTheNodeName()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest\TestEditPageFixture')
        );

        $client = static::createClient();
        $this->goToPageListAndAssertData($client, array(array('To be edited', '', '/to-be-edited')));

        $this->updatePage(
            $client,
            '/cms/content',
            'to-be-edited',
            array('page[title]' => 'To be edited'),
            'to-be-edited',
            'To be edited'
        );

        $this->goToPageListAndAssertData($client, array(array('To be edited', '', '/to-be-edited')));
    }

    public function testChangePageTitleShouldKeepOldRouteWhichRedirectsToNewRoute()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest\TestEditPageFixture')
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/to-be-edited');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('To be edited', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));

        $this->updatePage(
            $client,
            '/cms/content',
            'to-be-edited',
            array('page[title]' => 'New route'),
            'new-route',
            'New route'
        );

        $crawler = $client->request('GET', '/new-route');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('New route', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));

        $client->request('GET', '/to-be-edited');
        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();
        $this->assertContains('/new-route', $client->getRequest()->getUri());
        $this->assertEquals('New route', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));

        $crawler = $this->goToPageListAndAssertData($client, array(array('New route', '', '/new-route')));
        $routeCellText = $crawler->filter('table tbody tr')->eq(0)->filter('td')->eq(3)->text();
        $this->assertNotContains('/to-be-edited', $routeCellText);
    }

    public function testCreatePageWithSameTitleOfOtherPage()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest\TestEditPageFixture')
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/to-be-edited');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('To be edited', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));

        $this->createPage($client, 'To be edited', 'Lorem ipsum dolor', 'to-be-edited-1');
        $this->goToPageListAndAssertData(
            $client,
            array(array('To be edited', '', '/to-be-edited'), array('To be edited', '', '/to-be-edited-1'))
        );

        $crawler = $client->request('GET', '/to-be-edited-1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('To be edited', trim($crawler->filter('h2')->text()));
        $this->assertEquals('Lorem ipsum dolor', trim($crawler->filter('p')->text()));
    }

    public function testCreatePageWithMainMenuNode()
    {
        $client = static::createClient();
        $client->request('GET', '/my-new-page');
        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->createPage(
            $client,
            'My New Page',
            'Lorem ipsum dolor',
            'my-new-page',
            array(array('parent' => '/cms/menu/main', 'label' => 'New Page'))
        );
        $this->goToPageListAndAssertData($client, array(array('My New Page', 'Main Menu > New Page', '/my-new-page')));

        $crawler = $client->request('GET', '/my-new-page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('My New Page', trim($crawler->filter('h2')->text()));
        $this->assertEquals('Lorem ipsum dolor', trim($crawler->filter('p')->text()));
        $this->assertCount(1, $crawler->filter('nav#menu-main>ul>li'));
        $this->assertEquals('New Page', trim($crawler->filter('nav#menu-main>ul>li')->eq(0)->text()));
        $this->assertContains(
            '/my-new-page',
            $crawler->filter('nav#menu-main>ul>li')->eq(0)->filter('a')->eq(0)->attr('href')
        );
    }

    public function testSubPageShouldHaveSubRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/parent-page');
        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $client->request('GET', '/parent-page/sub-page');
        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->createPage(
            $client,
            'Parent Page',
            'Lorem ipsum dolor',
            'parent-page',
            array(array('parent' => '/cms/menu/main', 'label' => 'Parent Page'))
        );
        $this->createPage(
            $client,
            'Sub Page',
            'Lorem ipsum dolor',
            'sub-page',
            array(array('parent' => '/cms/menu/main/parent-page', 'label' => 'Sub Page'))
        );
        $this->goToPageListAndAssertData(
            $client,
            array(
                array('Parent Page', 'Main Menu > Parent Page', '/parent-page'),
                array('Sub Page', 'Main Menu > Parent Page > Sub Page', '/parent-page/sub-page'),
            )
        );
        $crawler = $client->request('GET', '/parent-page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $crawler = $client->request('GET', '/parent-page/sub-page');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDefinePageSeoMetadataShouldUseThatSpecificSeoMetadata()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest\TestEditPageFixture')
        );

        $client = static::createClient();

        $crawler = $client->request('GET', '/to-be-edited');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('To be edited', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));
        $this->assertSeoTitleEquals('To be edited - PUGX Cmf Page Bundle', $crawler);
        $this->assertSeoDescriptionEquals(
            'Symonfy2 bundle that provides pages content management system for Symfony2 built on top of Symfony CMF ' .
            'and Sonata Admin bundle.',
            $crawler
        );
        $this->assertSeoKeywordsEquals('symfony2, cmf, sonata, pugx', $crawler);

        $this->goToPageListAndAssertData($client, array(array('To be edited', '', '/to-be-edited')));

        $this->updatePage(
            $client,
            '/cms/content',
            'to-be-edited',
            array(
                'page[seoMetadata][title]' => 'Specific SEO title',
                'page[seoMetadata][metaDescription]' => 'Specific SEO description',
                'page[seoMetadata][metaKeywords]' => 'specific, seo, keywords',
            ),
            'to-be-edited',
            'To be edited'
        );

        $this->goToPageListAndAssertData($client, array(array('To be edited', '', '/to-be-edited')));

        $crawler = $client->request('GET', '/to-be-edited');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('To be edited', trim($crawler->filter('h2')->text()));
        $this->assertEquals('This page has to be edited soon.', trim($crawler->filter('p')->text()));
        $this->assertSeoTitleEquals('Specific SEO title - PUGX Cmf Page Bundle', $crawler);
        $this->assertSeoDescriptionEquals('Specific SEO description', $crawler);
        $this->assertSeoKeywordsEquals('symfony2, cmf, sonata, pugx, specific, seo, keywords', $crawler);
    }

    /**
     * @param Client $client
     * @param $title
     * @param $text
     * @param $expectedSlug
     * @param null|array $menuNodes
     */
    private function createPage(Client $client, $title, $text, $expectedSlug, $menuNodes = null)
    {
        $crawler = $client->request('GET', '/admin/cmf/page/page/create?uniqid=page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Create')->form();
        $form['page[title]'] = $title;
        $form['page[text]'] = $text;
        if ($menuNodes) {
            foreach ($menuNodes as $i => $menuNode) {
                $this->addMenuItemForm($form, 'pugx_cmf_page.page_admin', 'menuNodes');
                $form["page[menuNodes][$i][parent]"] = $menuNode['parent'];
                $form["page[menuNodes][$i][label]"] = $menuNode['label'];
            }
        }
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertContains(
            '/admin/cmf/page/page/cms/content/' . $expectedSlug . '/edit',
            $client->getRequest()->getUri()
        );

        $this->assertContains(
            "Item \"$title\" has been successfully created.",
            $crawler->filter('.alert.alert-success')->text()
        );
    }

    /**
     * @param $client
     * @param $expectedData
     * @return Crawler
     */
    private function goToPageListAndAssertData($client, $expectedData)
    {
        $crawler = $client->request('GET', '/admin/cmf/page/page/list');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $rows = $crawler->filter('table tbody tr');
        $this->assertCount(count($expectedData), $rows);
        foreach ($expectedData as $i => $expectedRow) {
            $row = $rows->eq($i);
            foreach ($expectedRow as $j => $expectedField) {
                $fieldIndex = $j + 1; // First column is always the checkbox/batch column
                $field = $row->filter('td')->eq($fieldIndex);
                if (empty($expectedField)) {
                    $this->assertEmpty(
                        trim($field->text()),
                        "Failed asserting that column at index $fieldIndex on row $i is empty."
                    );
                    continue;
                }
                $this->assertContains(
                    $expectedField,
                    $field->text(),
                    "Failed asserting that column at index $fieldIndex on row $i contains expected '$expectedField'."
                );
            }
        }
        return $crawler;
    }

    /**
     * @param $client
     * @param $parentPath
     * @param $nodeName
     * @param $editData
     * @param $expectedUpdatedNodeName
     * @param $expectedTitle
     */
    private function updatePage($client, $parentPath, $nodeName, $editData, $expectedUpdatedNodeName, $expectedTitle)
    {
        $nodePath = $parentPath . '/' . $nodeName;
        $expectedUpdatedPath = $parentPath . '/' . $expectedUpdatedNodeName;
        $crawler = $client->request('GET', '/admin/cmf/page/page' . $nodePath . '/edit?uniqid=page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Update')->form();
        $form->setValues($editData);
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertContains(
            '/admin/cmf/page/page' . $expectedUpdatedPath . '/edit',
            $client->getRequest()->getUri()
        );
        $this->assertContains(
            'Item "' . $expectedTitle . '" has been successfully updated.',
            $crawler->filter('.alert.alert-success')->text()
        );
    }

    /**
     * @param Form $form
     * @param $code
     * @param $elementId
     * @return Crawler
     */
    private function addMenuItemForm(Form $form, $code, $elementId)
    {
        $ajaxClient = static::createClient();
        $ajaxCrawler = $ajaxClient->request(
            'POST',
            '/admin/core/append-form-field-element',
            array_merge(
                $form->getPhpValues(),
                array(
                    'code' => $code,
                    'elementId' => 'page_' . $elementId,
                    'uniqid' => 'page'
                )
            )
        );
        foreach ($ajaxCrawler->filter('input') as $node) {
            if ($node->attributes->getNamedItem('type')) {
                if ($node->attributes->getNamedItem('type')->nodeValue == 'checkbox' ||
                    $node->attributes->getNamedItem('type')->nodeValue == 'radio') {
                    $form->set(new ChoiceFormField($node));
                    continue;
                }
                if ($node->attributes->getNamedItem('type') == 'file') {
                    $form->set(new FileFormField($node));
                    continue;
                }
            }

            $form->set(new InputFormField($node));
        }
        foreach ($ajaxCrawler->filter('select') as $node) {
            $form->set(new ChoiceFormField($node));
        }
        foreach ($ajaxCrawler->filter('textarea') as $node) {
            $form->set(new TextareaFormField($node));
        }
        return $ajaxCrawler;
    }

    /**
     * @param $expectedTitle
     * @param $crawler
     */
    private function assertSeoTitleEquals($expectedTitle, $crawler)
    {
        $this->assertEquals($expectedTitle, trim($crawler->filter('title')->text()));
        $this->assertEquals(
            $expectedTitle,
            trim($crawler->filter('meta[name=title]')->attr('content'))
        );
    }

    /**
     * @param $expectedDescription
     * @param $crawler
     */
    private function assertSeoDescriptionEquals($expectedDescription, $crawler)
    {
        return $this->assertEquals(
            $expectedDescription,
            trim($crawler->filter('meta[name=description]')->attr('content'))
        );
    }

    /**
     * @param $expectedKeywords
     * @param $crawler
     */
    private function assertSeoKeywordsEquals($expectedKeywords, $crawler)
    {
        $this->assertEquals($expectedKeywords, $crawler->filter('meta[name=keywords]')->attr('content'));
    }

}
