<?php

namespace Microsite;

abstract class Page_DisplayMedia extends Page
{
    protected function processActions()
    {
        $key = $this->request->registerParam('media')->setMD5()->get();
        
        if(empty($key))
        {
            $this->redirectWithErrorMessage(
                t('Cannot display media:').' '.
                t('Unknown media key specified.'),
                $this->site->getDefaultPage()->buildURL()
            );
        }
        
        if(!isset($_SESSION['displaymedia']) || !isset($_SESSION['displaymedia'][$key]))
        {
            $this->redirectWithErrorMessage(
                t('Cannot display media:').' '.
                t('No media key registered in the session.'),
                $this->site->getDefaultPage()->buildURL()
            );
        }
        
        $config = $_SESSION['displaymedia'][$key];
        
        $path = $this->handleMediaPath($config['path']);
        
        \AppUtils\FileHelper::sendFile($path, null, false);
    }
    
    protected function handleMediaPath(string $path) : string
    {
        return $path;
    }
    
    public function getPageAbstract(): string { return ''; }
    public function getPageTitle(): string { return ''; }
    public function getNavigationTitle() : string { return ''; }
    public function getDefaultSlug(): string { return ''; }
    protected function initNavigation(): void {}
    protected function _renderContent(): string {}
}