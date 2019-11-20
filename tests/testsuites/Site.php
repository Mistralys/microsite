<?php

final class SiteTest extends \MicrositeTestSite\TestCase
{
    public function test_boot()
    {
        $site = $this->createSite();
        
        $this->assertInstanceOf(\Microsite\Site::class, $site);
    }
    
    public function test_getPages()
    {
        $site = $this->createSite();
        
        $default = $site->getDefaultPage();
        
        $this->assertInstanceOf(\MicrositeTestSite\Page_Default::class, $default);
        $this->assertEquals('Default', $default->getSlug());
    }
}
