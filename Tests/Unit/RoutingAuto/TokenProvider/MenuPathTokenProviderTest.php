<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/10/15
 * Time: 01:31
 */

namespace PUGX\Cmf\PageBundle\Test\Unit\RoutingAuto\TokenProvider;


use PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\MenuPathTokenProvider;

class MenuPathTokenProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvideValueWithTwoLevelPath()
    {
        $slugifier = $this->mockSlugifier(
            array('Subject' => 'subject', 'Parent Subject' => 'parent-subject')
        );
        $documentManager = $this->getMock('Doctrine\ODM\PHPCR\DocumentManager', array(), array(), '', false);
        $subject = $this->getMock(
            'PUGX\Cmf\PageBundle\Tests\Unit\RoutingAuto\TokenProvider\InitialTestSubjectInterface'
        );
        $subject->expects($this->any())->method('provideRouteToken')->willReturn('Subject');

        $menu = $this->getMock('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu');
        $parentContent = $this->getMock(
            'PUGX\Cmf\PageBundle\Tests\Unit\RoutingAuto\TokenProvider\InitialTestSubjectInterface'
        );
        $parentContent->expects($this->any())->method('provideRouteToken')->willReturn('Parent Subject');
        $parentContent->expects($this->any())->method('providePrimaryMenuNode')->willReturn($menu);
        $parentMenuNode = $this->mockMenuNode(null, $parentContent);
        $menuNode = $this->mockMenuNode($parentMenuNode);
        $subject->expects($this->any())->method('providePrimaryMenuNode')->willReturn($menuNode);
        $uriContext = $this->mockUriContext($subject);
        $provider = new MenuPathTokenProvider($slugifier, $documentManager);
        $path = $provider->provideValue($uriContext, array());
        $this->assertEquals('parent-subject/subject', $path);
    }

    public function testProvideValueWithRouteTokenProviderSubjectButNoMenuProviderSubjectShouldReturnOnlyRouteToken()
    {
        $slugifier = $this->mockSlugifier(array('My Subject' => 'my-subject'));
        $documentManager = $this->getMock('Doctrine\ODM\PHPCR\DocumentManager', array(), array(), '', false);
        $subject = $this->getMock('PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\RouteTokenProviderInterface');
        $subject->expects($this->any())->method('provideRouteToken')->willReturn('My Subject');
        $uriContext = $this->mockUriContext($subject);
        $provider = new MenuPathTokenProvider($slugifier, $documentManager);
        $path = $provider->provideValue($uriContext, array());
        $this->assertEquals('my-subject', $path);
    }

    public function testProvideValueWithNoRouteTokenProviderSubjectButMenuProviderSubjectShouldReturnEmpty()
    {
        $slugifier = $this->mockSlugifier(array());
        $documentManager = $this->getMock('Doctrine\ODM\PHPCR\DocumentManager', array(), array(), '', false);
        $subject = $this->getMock('PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\PrimaryMenuNodeProviderInterface');
        $parentContent = $this->getMock('PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\RouteTokenProviderInterface');
        $parentContent->expects($this->any())->method('provideRouteToken')->willReturn('Parent Subject');
        $parentMenuNode = $this->mockMenuNode(null, $parentContent);
        $menuNode = $this->mockMenuNode($parentMenuNode, null);
        $subject->expects($this->any())->method('providePrimaryMenuNode')->willReturn($menuNode);
        $uriContext = $this->mockUriContext($subject);
        $provider = new MenuPathTokenProvider($slugifier, $documentManager);
        $path = $provider->provideValue($uriContext, array());
        $this->assertEquals('', $path);
    }

    public function testProvideValueWithNoRouteTokenProviderSubjectAndNoMenuProviderSubject()
    {
        $slugifier = $this->mockSlugifier(array());
        $documentManager = $this->getMock('Doctrine\ODM\PHPCR\DocumentManager', array(), array(), '', false);
        $subject = $this->getMock('\stdClass');
        $uriContext = $this->mockUriContext($subject);
        $provider = new MenuPathTokenProvider($slugifier, $documentManager);
        $path = $provider->provideValue($uriContext, array());
        $this->assertEquals('', $path);
    }

    public function testProvideValueForTranslatedContent()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * @param $parentObject
     * @param null $content
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockMenuNode($parentObject = null, $content = null)
    {
        $menuNode = $this->getMock('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode');
        if ($parentObject) {
            $menuNode->expects($this->any())->method('getParentObject')->willReturn($parentObject);
        }
        if ($content) {
            $menuNode->expects($this->any())->method('getContent')->willReturn($content);
        }
        return $menuNode;
    }

    /**
     * @param $calls
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockSlugifier($calls)
    {
        $slugifier = $this->getMock('Symfony\Cmf\Bundle\CoreBundle\Slugifier\SlugifierInterface');
        foreach (array_keys($calls) as $i => $string) {
            $slugifier
                ->expects($this->at($i))
                ->method('slugify')
                ->with($string)
                ->willReturn($calls[$string])
            ;
        }
        return $slugifier;
    }

    /**
     * @param $subject
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockUriContext($subject)
    {
        $uriContext = $this->getMockBuilder('Symfony\Cmf\Component\RoutingAuto\UriContext')
            ->disableOriginalConstructor()
            ->getMock();
        $uriContext->expects($this->any())->method('getSubjectObject')->willReturn($subject);
        return $uriContext;
    }
}
