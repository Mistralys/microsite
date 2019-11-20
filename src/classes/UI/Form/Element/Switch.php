<?php

namespace Microsite;

class UI_Form_Element_Switch extends UI_Form_Element
{
    public function supportsValue()
    {
        return true;
    }
    
    public function supportsWrapper()
    {
        return true;
    }
    
    protected $variations;
    
    protected function getVariations()
    {
        return array(
            'yesno' => array(
                'label' => t('Yes/No'),
                'yes' => t('Yes'),
                'no' => t('No')
            ),
            'truefalse' => array(
                'label' => t('True/False'),
                'yes' => t('True'),
                'no' => t('False')
            ),
            'enabled' => array(
                'label' => t('Enabled/Disabled'),
                'yes' => t('Enabled'),
                'no' => t('Disabled')
            )
        );
    }
    
    protected function _renderElement() : string
    {
        $this->addClass('form-control');
        $this->setAttribute('value', $this->getValue());
        
        ob_start(); 
       
        $idY = $this->getID().'yes';
        $idN = $this->getID().'no';
        
        ?>
        	<div class="form-check form-check-inline">
        		<input class="form-check-input" type="checkbox" id="<?php echo $idN ?>" value="no">
        		<label class="form-check-label" for="<?php echo $idN ?>"><?php pt('Yes') ?></label>
        	</div>
        	<div class="form-check form-check-inline">
        		<input class="form-check-input" type="checkbox" id="<?php echo $idY ?>" value="yes">
        		<label class="form-check-label" for="<?php echo $idY ?>"><?php ?></label>
        	</div>
    	<?php 
        
        return ob_get_clean();
    }
}
