<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 11/07/15
 * Time: 14:28
 */

namespace PUGX\Cmf\Tests\WebTest;


use PUGX\Cmf\PageBundle\Test\IsolatedTestCase;

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
    }

    /**
     * @param $client
     * @param $title
     * @param $content
     * @param $expectedSlug
     */
    private function createPage($client, $title, $content, $expectedSlug)
    {
        $crawler = $client->request('GET', '/admin/cmf/page/page/create?uniqid=page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Create')->form();
        $form['page[title]'] = $title;
        $form['page[content]'] = $content;
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertContains(
            '/admin/cmf/page/page/cms/content/' . $expectedSlug . '/edit',
            $client->getRequest()->getUri()
        );

        $this->assertContains(
            'Item "my-new-page" has been successfully created.',
            $crawler->filter('.alert.alert-success')->text()
        );
    }

    /**
     * @param $client
     * @param $expectedData
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
    }

}
