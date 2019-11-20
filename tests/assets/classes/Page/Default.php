<?php

namespace MicrositeTestSite;

class Page_Default extends \Microsite\Page
{
    public function getPageAbstract(): string
    {
        return 'abstract';
    }
    
    public function getNavigationTitle(): string
    {
        return 'nav title';
    }
    
    public function getDefaultSlug(): string
    {
        return '';
    }
    
    protected function initNavigation(): void
    {
        
    }
    
    protected function _renderContent(): string
    {
        return 'content';
    }
    
    public function getPageTitle(): string
    {
        return 'page title';
    }
}
