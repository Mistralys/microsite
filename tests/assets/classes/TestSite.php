<?php

namespace MicrositeTestSite;

class Site extends \Microsite\Site
{
    public function getDocumentTitle(): string
    {
        return 'Test site';
    }

    public function getNavigationTitle(): string
    {
        return 'Test site';
    }

    public function getDefaultSlug(): string
    {
        return 'Default';
    }

    protected function initNavigation(): void
    {
        
    }
}
