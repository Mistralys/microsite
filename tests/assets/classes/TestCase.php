<?php

namespace MicrositeTestSite;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function createSite()
    {
        return \MicrositeTestSite\Site::boot(
            'MicrositeTestSite',
            TESTS_ROOT,
            'http://127.0.0.1/testsite'
        );
    }

    protected function createForm() : \Microsite\UI_Form
    {
        $site = $this->createSite();
        $page = $site->getDefaultPage();
        
        return $page->getForm();
    }
}
