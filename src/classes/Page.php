<?php

namespace Microsite;

abstract class Page
{
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
        // the parent page can be an instance of site, so we ignore it in this case
        if($parentPage && !$parentPage instanceof Site) 
        {
            $this->parentPage = $parentPage;
        }

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
    }
    
    abstract protected function initNavigation() : void;

    abstract public function getDefaultSlug() : string;
    
    abstract public function getPageTitle() : string;
    
    abstract public function getPageAbstract() : string;
    
    abstract public function getNavigationTitle() : string;
    
    abstract protected function _render() : string;
    
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
    
    protected function sendAjaxSuccess($data=array())
    {
        header('Content-Type:application/json; charset=UTF-8');
        
        echo json_encode(array(
            'status' => 'success',
            'data' => $data
        ));
        
        exit;
    }
    
    protected function sendAjaxError($message)
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
    
    public function display() : Page
    {
        echo $this->render();
        return $this;
    }
    
    public function render() : string
    {
        $this->initRender();
        
        $this->navigation = $this->ui->createNavigation()->addLimitParameter('action');
        $this->initNavigation();
        
        $this->initForm();
        
        $tpl = $this->ui->createTemplate('Page');
        $tpl->setVar('page', $this);
        $tpl->setVar('breadcrumb', $this->breadcrumb);
        $tpl->setVar('content', $this->_render());
        
        return $tpl->render();
    }
    
    protected function initRender()
    {
        
    }
    
    public function setSubactionAbstract($abstract)
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
    
    public function getID()
    {
        if(!isset($this->id)) {
            $this->id =  str_replace($this->site->getNamespace().'\Page_', '', get_class($this));
        }
        
        return $this->id;
    }
    
    public function buildURL($params=array())
    {
        $params['action'] = $this->getSlug(); 
        
        $url = rtrim($this->site->getWebrootURL(), '/');
        
        $query = http_build_query($params);
        
        if(!empty($query)) {
            $url .= '/?'.$query;
        }
        
        return $url;
    }
    
    public function redirectWithErrorMessage($message, $url)
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_ERROR, $message, $url);
    }

    public function redirectWithInfoMessage($message, $url)
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_INFO, $message, $url);
    }
    
    public function redirectWithSuccessMessage($message, $url)
    {
        $this->redirectWithMessage(Site_Message::MESSAGE_TYPE_SUCCESS, $message, $url);
    }
    
    function redirectWithMessage($type, $message, $url)
    {
        $this->site->addMessage($type, $message);
        $this->redirect($url);
    }
    
    public function redirect($url)
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
        
        if($this->hasParent()) {
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

    public function addWarning($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_WARNING, $message);
    }
    
    public function addError($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_ERROR, $message);
    }
    
    public function addInfo($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }
    
    public function addNotice($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }
    
    protected function addMessage($type, $message) : Site_Message
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
     * @throws \Exception
     * @return Page[]
     */
    public function initPages()
    {
        $pagesFolder = $this->getOwnFolder();

        if(!is_dir($pagesFolder)) {
            return;
        }
        
        $names = \AppUtils\FileHelper::createFileFinder($pagesFolder)->getPHPClassNames();
        
        foreach($names as $name)
        {
            if($this->parentPage) {
                $name = str_replace('.', '_', $this->parentPage->getSlug()).'_'.$name;
            }
            
            $className = $this->namespace.'\\Page_'.$name;
            
            if(!class_exists($className)) {
                throw new \Exception(
                    sprintf(
                        'Cannot initialize page [%s], the class [%s] was not found.',
                        $name,
                        $className
                    ),
                    self::ERROR_CANNOT_INSTANTIATE_PAGE
                );
            }
            
            $page = new $className($this->site, $this);
            
            if(!$page instanceof Page) {
                throw new \Exception(
                    sprintf(
                        'The page [%s] is not a \Microsite\Page class instance.',
                        $className
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