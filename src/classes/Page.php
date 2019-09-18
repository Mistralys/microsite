<?php

namespace Microsite;

abstract class Page
{
   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
    protected $subaction;
    
    protected $subactions;
    
    protected $submethod;
    
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
    
    public function __construct(Site $site)
    {
        $this->request = new \AppUtils\Request();
        $this->site = $site;
        $this->ui = $site->getUI();
        $this->breadcrumb = new UI_Breadcrumb($this);
        $this->form = new UI_Form($this);
        
        $ajax = $this->request->getParam('ajaxMethod');
        if(!empty($ajax)) 
        {
            $method = 'ajax_'.$ajax;
            if(method_exists($this, $method)) {
                $this->$method();
                exit;
            }
            
            $this->sendAjaxError('No such ajax method');
        }
        
        $this->resolveSubaction();
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
    
    abstract public function getPageTitle() : string;
    
    abstract public function getPageAbstract() : string;
    
    abstract public function getNavigationTitle() : string;
    
    public function render() : string
    {
        $headVars = array(
            'pageBaseURL' => $this->buildURL(),
            'pageAction' => $this->getURLName(),
            'pageSubaction' => $this->subaction
        );
        
        if(isset($this->app)) {
            $headVars['appid'] = $this->app->getID();
        }
        
        if(isset($this->rep)) {
            $headVars['repid'] = $this->rep->getID();
        }
        
        foreach($headVars as $name => $value) {
            $this->ui->addJSHead(sprintf(
                "var %s = %s;",
                $name,
                json_encode($value)
            ));
        }

        ob_start();
        $html = call_user_func(array($this, $this->submethod));
        $html = ob_get_clean().$html;
        
        $this->initForm();
        
        if(!empty($this->subactions)) 
        {
            $nav =
    		'<ul class="nav nav-pills">';
                $data = array(
                    'action' => $this->getURLName()
                );
                
        		foreach($this->subactions as $name => $def) 
        		{
        		    $active = '';
        		    if($name == $this->subaction) {
        		        $active = ' class="active"';
        		    }
        		    
        		    $urlData = $data;
        		    $urlData['subaction'] = $name;
        		    
        		    $url = '?'.http_build_query($urlData);
        		    
        		    $nav .= '<li role="presentation"'.$active.'><a href="'.$url.'">'.$def['label'].'</a></li>';
        		}
                $nav .=
    		'</ul>'.
            '<br/>';
                
            if(isset($this->subactionAbstract)) {
                $nav .= '<p>'.$this->subactionAbstract.'</p><hr>';
            }
                
    		$html = $nav.$html;      
        }
        
        $abstract = $this->getPageAbstract();
        if(!empty($abstract)) {
            $html = '<p>'.$abstract.'</p><hr>'.$html;
        }
        
        $title = $this->getPageTitle();
        if(!empty($title)) {
            $html = '<h2>'.$title.'</h2>'.$html;
        }
        
        $html = $this->breadcrumb->render().$html;
        
        return $html;
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
    
    public function getURLName()
    {
        return strtolower($this->getID());
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
        $params['action'] = $this->getURLName(); 
        
        return $this->site->getWebrootURL().'/?'.http_build_query($params);
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

    abstract protected function _render_default();
    
    abstract protected function _initSubactions();
    
   /**
    * @var UI_Form
    */
    protected $form;
    
    protected function initForm()
    {
        $this->form
        ->setHidden('action', $this->getURLName())
        ->setHidden('subaction', $this->subaction);
    }
    
    protected function addSubaction($name, $label, $private=false, $default=false)
    {
        $this->subactions[$name] = array(
            'label' => $label,
            'private' => $private,
            'default' => $default
        ); 
    }
    
    protected function resolveSubaction()
    {
        $this->_initSubactions();
        
        $subaction = 'default';
        
        if(!empty($this->subactions))
        {
            $name = $this->request->getParam('subaction');
            if(!empty($name))
            {
                $subaction = $name;
            }
            else 
            {
                foreach($this->subactions as $aname => $adef) {
                    if($adef['default']) {
                        $name = $aname;
                        break;
                    }
                }
            }
            
            if(isset($this->subactions[$name])) {
                $subaction = $name;
            }
        }
        
        $this->submethod = '_render_'.str_replace('-', '_', $subaction);
        $this->subaction = $subaction;
        
        if(!method_exists($this, $this->submethod)) {
            throw new \Exception(sprintf('The page [%s] does not have a subaction [%s].', $this->getID(), $this->subaction));
        }
    }
    
    public function isActive() : bool
    {
        return $this->site->getActivePage() === $this;
    }
}