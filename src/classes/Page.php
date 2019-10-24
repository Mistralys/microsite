<?php

declare(strict_types=1);

namespace Microsite;

abstract class Page implements Interface_Renderable
{
    use Traits_Renderable;
    
    const ERROR_CANNOT_INSTANTIATE_PAGE = 38201;
    
    const ERROR_NOT_A_PAGE_INSTANCE = 38202;
    
   /**
    * @var \AppUtils\Request
    */
    protected $request;

   /**
    * @var UI_Breadcrumb
    */
    protected $breadcrumb;
    
   /**
    * @var Site
    */
    protected $site;
    
   /**
    * @var UI
    */
    protected $ui;
    
   /**
    * @var Page
    */
    protected $parentPage = null;
    
    /**
     * @var UI_Navigation
     */
    protected $navigation;
    
   /**
    * @var Page[]
    */
    protected static $pages = array();

   /**
    * @var Page[]
    */
    protected $subpages = array();
    
    public function __construct(Site $site, Page $parentPage=null)
    {
        $this->parentPage = $parentPage;
        
        // avoid setting these for the main site instance
        if(!$this instanceof Site) 
        {
            $this->request = $site->getRequest();
            $this->ui = $site->getUI();
        }
        
        $this->site = $site;
        $this->breadcrumb = new UI_Breadcrumb($this);
        $this->form = new UI_Form($this);

        $this->initPages();
        $this->init();
    }
    
   /**
    * Called right after the constructor completes.
    * This can be extended to add custom page 
    * initializations that can be done at this stage.
    * 
    * Otherwise, prefer the method {@link \Microsite\Page::processActions()}.
    * 
    * @see \Microsite\Page::processActions()
    */
    protected function init()
    {
        // can be extended
    }
    
    abstract protected function initNavigation() : void;

    abstract public function getDefaultSlug() : string;
    
    abstract public function getPageTitle() : string;
    
    abstract public function getPageAbstract() : string;
    
    abstract public function getNavigationTitle() : string;
    
    abstract protected function _renderContent() : string;
    
    public function getSite() : Site
    {
        return $this->site;
    }
    
    public function getNavigation() : UI_Navigation
    {
        return $this->navigation;
    }
    
    public function handleAJAX()
    {
        $ajax = $this->request->getParam('ajaxMethod');
        if(empty($ajax)) {
            return;
        }
        
        $method = 'ajax_'.$ajax;
        if(method_exists($this, $method)) {
            $this->$method();
            exit;
        }
        
        $this->sendAjaxError('No such ajax method');
    }
    
    protected function sendAjaxSuccess(array $data=array())
    {
        header('Content-Type:application/json; charset=UTF-8');
        
        echo json_encode(array(
            'status' => 'success',
            'data' => $data
        ));
        
        exit;
    }
    
    protected function sendAjaxError(string $message)
    {
        header('Content-Type:application/json; charset=UTF-8');
        
        echo json_encode(array(
            'status' => 'error',
            'message' => $message
        ));
        
        exit;
    }
    
    public function getRequest() : \AppUtils\Request
    {
        return $this->request;
    }
    
    protected function preRender()
    {
        $this->processActions();
        
        $this->initRender();
        
        $this->navigation = $this->ui->createNavigation()->addLimitParameter('action');
        $this->initNavigation();
        
        $this->initForm();
    }
    
    protected function _render() : string
    {
        $tpl = $this->ui->createTemplate('Page');
        $tpl->setVar('page', $this);
        $tpl->setVar('breadcrumb', $this->breadcrumb);
        $tpl->setVar('content', $this->_renderContent());
        
        return $tpl->render();
    }
    
    protected function processActions()
    {
        
    }
    
    protected function initRender()
    {
        
    }
    
    public function setSubactionAbstract(string $abstract) : Page
    {
        $this->subactionAbstract = $abstract;
        return $this;
    }
    
    public function getSubactionLabel($name=null)
    {
        if(empty($this->subactions)) {
            return null;
        }
        
        if(empty($name)) {
            $name = $this->subaction;
        }
        
        return $this->subactions[$name];
    }
    
    protected $id;
    
    public function getID() : string
    {
        if(!isset($this->id)) 
        {
            if($this instanceof Site) {
                $this->id = 'Site';
            } else {
                $tokens = explode('_', get_class($this));
                $this->id = array_pop($tokens);
            }
        }
        
        return $this->id;
    }
    
    public function buildURL(array $params=array()) : string
    {
        $params['action'] = $this->getSlug(); 
        
        $url = rtrim($this->site->getWebrootURL(), '/');
        
        $query = http_build_query($params);
        
        if(!empty($query)) {
            $url .= '/?'.$query;
        }
        
        return $url;
    }
    
    public function redirectWithErrorMessage(string $message, string $url) : void
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_ERROR, $message, $url);
    }

    public function redirectWithInfoMessage(string $message, string $url) : void
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_INFO, $message, $url);
    }
    
    public function redirectWithSuccessMessage(string $message, string $url) : void
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_SUCCESS, $message, $url);
    }
    
    function redirectWithMessage(string $type, string $message, string $url) : void
    {
        $this->addMessage($type, $message);
        $this->redirect($url);
    }
    
    public function redirect(string $url) : void
    {
        ob_end_clean();
        
        header('Location:'.$url);
        exit;
    }

   /**
    * @var UI_Form
    */
    protected $form;
    
    protected function initForm()
    {
        $this->form->setHidden('action', $this->getSlug());
    }
    
    protected function addSubaction($name, $label, $private=false, $default=false)
    {
        $this->subactions[$name] = array(
            'label' => $label,
            'private' => $private,
            'default' => $default
        ); 
    }
    
    public function hasParent() : bool
    {
        return isset($this->parentPage);
    }
    
   /**
    * Retrieves a slug of page IDs, in case this
    * page has one or more parent pages. If it has no
    * parents, it is like calling getID().
    * 
    * @return string Example: Page1.Page2.Page3 (From highest parent to this one)
    */
    public function getSlug() : string
    {
        $path = $this->getID();
        
        if($this->hasParent() && !$this->parentPage instanceof Site) {
            $path = $this->parentPage->getSlug().'.'.$path;
        }
        
        return $path;
    }
    
    protected function getOwnFolder() : string
    {
        return sprintf(
            '%s/Page/%s',
            $this->site->getClassesFolder(),
            str_replace('.', '/', $this->getSlug())
        );
    }
    
    public function isActive() : bool
    {
        return $this->site->getActivePage() === $this;
    }
    
    public function getUI() : UI
    {
        return $this->ui;
    }

    public function addWarning(string $message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_WARNING, $message);
    }
    
    public function addError(string $message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_ERROR, $message);
    }
    
    public function addInfo(string $message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }
    
    public function addNotice(string $message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }
    
    protected function addMessage(string $type, string $message) : Site_Message
    {
        $message = new Site_Message($type, $message);
        
        $_SESSION['messages'][] = $message;
        
        return $message;
    }

    /**
     * Creates page instances from the target folder.
     *
     * @param string $pagesFolder
     * @param Page|NULL $parentPage
     * @throws Exception
     */
    public function initPages() : void
    {
        $pagesFolder = $this->getOwnFolder();

        if(!is_dir($pagesFolder)) {
            return;
        }
        
        $names = \AppUtils\FileHelper::createFileFinder($pagesFolder)->getPHPClassNames();
        
        foreach($names as $name)
        {
            if(!$this instanceof Site) {
                $name = str_replace('.', '_', $this->getSlug()).'_'.$name;
            }
            
            $className = '\\'.$this->site->getNamespace().'\\Page_'.$name;
            
            if(!class_exists($className)) 
            {
                throw new Exception(
                    sprintf(
                        'Cannot initialize page [%s], the class [%s] was not found.',
                        $name,
                        $className
                    ),
                    self::ERROR_CANNOT_INSTANTIATE_PAGE
                );
            }
            
            $page = new $className($this->site, $this);
            
            if(!$page instanceof Page) 
            {
                throw new Exception(
                    sprintf(
                        'The page [%s] is not a [%s] class instance.',
                        $className,
                        __CLASS__
                    ),
                    self::ERROR_NOT_A_PAGE_INSTANCE
                );
            }
            
            self::$pages[$page->getSlug()] = $page;
            
            $this->subpages[] = $page;
        }
    }

   /**
    * Retrieves a specific page by its slug.
    * @param string $slug
    * @return Page|NULL
    */
    public function getPageBySlug(string $slug) : ?Page
    {
        if(isset(self::$pages[$slug])) {
            return self::$pages[$slug];
        }
        
        return null;
    }
    
   /**
    * Retrieves a list of the slugs of all available pages.
    * @return array
    */
    public function getSlugs() : array
    {
        return array_keys(self::$pages);
    }
    
   /**
    * Retrieves all subpages of the current page.
    * @return \Microsite\Page[]
    */
    public function getSubpages()
    {
        return $this->subpages;
    }
}