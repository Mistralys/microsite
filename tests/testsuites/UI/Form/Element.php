<?php

final class UI_Form_ElementTest extends \MicrositeTestSite\TestCase
{
    public function test_setAttribute()
    {
        $form = $this->createForm();
        
        $text = $form->addText('testtext', 'Test text');
        
        $text->setAttribute('foo', 'bar');
        
        $this->assertEquals('bar', $text->getAttribute('foo'));
    }
    
    public function test_elementText()
    {
        $form = $this->createForm();
        
        $text = $form->addText('testtext', 'Test text')
        ->setPlaceholder('placeholder')
        ->setRows(5)
        ->setColumns(80);
        
        $this->assertEquals('testtext', $text->getName());
        $this->assertEquals('placeholder', $text->getAttribute('placeholder'));
        $this->assertEquals('5', $text->getAttribute('rows'));
        $this->assertEquals('80', $text->getAttribute('cols'));
    }
}
