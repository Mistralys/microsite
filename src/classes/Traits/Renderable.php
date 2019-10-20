<?php
/**
 * File containing the {@link Traits_Renderable} trait and {@link Interface_Renderable} interface.
 * 
 * @package Microsite
 * @subpackage Traits
 * @see Traits_Renderable
 * @see Interface_Renderable
 */

declare(strict_types=1);

namespace Microsite;

/**
 * Trait for renderable objects. These only have to implement the
 * <code>_render()</code> method, and the rest is handled by the 
 * trait. Do not forget to add the interface as well.
 * 
 * @package Microsite
 * @subpackage Traits
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @see Interface_Renderable
 */
trait Traits_Renderable
{
   /**
    * Renders the object.
    * 
    * Note: the preRender() method is called first,
    * so any initialization can be done by extending
    * that method.
    * 
    * @return string
    * @see Traits_Renderable::preRender()
    */
    public function render() : string
    {
        $this->preRender();
        
        $content = $this->_render();
        return $content;
    }
    
   /**
    * Called just before rendering the object. Can
    * be extended to execute any necessary tasks
    * that need to be done first, if any.
    */
    protected function preRender()
    {
        // can be extended 
    }
    
   /**
    * Echos the rendered content and exits the script.
    */
    public function display() : void
    {
        echo $this->render();
    }
    
    public function __toString()
    {
        return $this->render();
    }
    
    abstract protected function _render() : string;
}

/**
 * Base interface for renderable objects.
 * 
 * @package Microsite
 * @subpackage Traits
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @see Traits_Renderable
 */
interface Interface_Renderable
{
    public function render() : string;
    
    public function display() : void;
}