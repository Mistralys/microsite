<?php

final class UI_FormTest extends \MicrositeTestSite\TestCase
{
    protected function setUp() : void
    {
        $_REQUEST = array();
    }
    
    public function test_setHidden()
    {
        $form = $this->createForm();
        
        $form->setHidden('foo', 'bar');
        $form->setHidden('bar', 'foo');
        
        $this->assertEquals('bar', $form->getHidden('foo')->getValue());
        $this->assertEquals('foo', $form->getHidden('bar')->getValue());
    }
    
    public function test_setHidden_otherElementType()
    {
        $form = $this->createForm();
        
        $form->addText('foo', 'Foo');
        
        $this->expectException(\Microsite\Exception::class);
        
        $form->setHidden('foo', 'value');
    }

    public function test_getHidden()
    {
        $form = $this->createForm();

        $this->assertNull($form->getHidden('foo'));
        
        $form->setHidden('foo', 'bar');
        
        $hidden = $form->getHidden('foo');
        
        $this->assertInstanceOf(\Microsite\UI_Form_Element_Hidden::class, $hidden);
        $this->assertEquals('foo', $hidden->getName());
    }
    
   /**
    * Trying to fetch a hidden element from a name that 
    * exists but is not a hidden element should return null.
    */
    public function test_getHidden_wrongElementType()
    {
        $form = $this->createForm();
        
        $form->addText('foo', 'Foo');
        
        $this->assertNull($form->getHidden('foo'));
    }
    
    public function test_getElementByName()
    {
        $form = $this->createForm();
        
        $form->addText('foo', 'Foo');
        $form->setHidden('hidden', 'value');
        
        $text = $form->getElementByName('foo');
        $hidden = $form->getElementByName('hidden');
        
        $this->assertInstanceOf(\Microsite\UI_Form_Element_Text::class, $text);
        $this->assertInstanceOf(\Microsite\UI_Form_Element_Hidden::class, $hidden);
    }
    
    public function isSubmitted()
    {
        $form = $this->createForm();
        
        $this->assertFalse($form->isSubmitted());
        
        $_REQUEST[$form->getSubmitVarName()] = 'yes';
        
        $this->assertTrue($form->isSubmitted());
    }
    
    public function test_getValues()
    {
        $form = $this->createForm();
        
        $form->setHidden('hidden', 'value');
        
        $form->addText('text', 'Text');
        
        $this->assertEquals(array('hidden' => 'value', 'text' => ''), $form->getValues());
    }
}
