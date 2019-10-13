<?php
/**
 * File containing the {@link Traits_Classable} trait and {@link Interface_Classable} interface.
 *
 * @package Microsite
 * @subpackage Traits
 * @see Traits_Classable
 * @see Interface_Classable
 */

declare(strict_types=1);

namespace Microsite;

/**
 * Trait for objects than can have an HTML class. Handles all 
 * the setting and getting of classes.
 *
 * @package Microsite
 * @subpackage Traits
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @see Interface_Classable
 */
trait Traits_Classable
{
    protected $classes = array();
    
    public function addClass(string $name)
    {
        if(!in_array($name, $this->classes)) {
            $this->classes[] = $name;
        }
        
        return $this;
    }
    
    public function addClasses(array $names)
    {
        foreach($names as $name) {
            $this->addClass($name);
        }
        
        return $this;
    }
    
    public function removeClass(string $name)
    {
        $key = array_search($name, $this->classes);
        
        if($key !== false) {
            unset($this->classes[$key]);
        }
        
        return $this;
    }
    
    public function hasClass(string $name) : bool
    {
        return in_array($name, $this->classes);
    }
    
    public function getClasses() : array
    {
        return $this->classes;
    }
    
    public function hasClasses() : bool
    {
        return !empty($this->classes);
    }
    
   /**
    * Retrieves the full space-separated class names string.
    * @return string
    */
    public function getClassesAsString() : string
    {
        sort($this->classes);
        
        return implode(' ', $this->classes);
    }
    
   /**
    * Retrieves the full <code>class=""</code> attribute
    * string, or an empty string if no classes have been added.
    * 
    * @return string
    */
    public function getClassAttribute() : string
    {
        if(!empty($this->classes)) {
            return ' class="'.$this->getClassesAsString().'" ';
        }
        
        return '';
    }
}

interface Interface_Classable
{
    public function addClass(string $name);
    
    public function removeClass(string $name);
    
    public function addClasses(array $names);
    
    public function getClasses() : array;
    
    public function hasClass(string $name) : bool;
    
    public function hasClasses() : bool;
    
    public function getClassesAsString() : string;
}