<?php
namespace Delovodnik\Form;

use Zend\Form\Form;
use Zend\Form\Element;

$date = new Element\Date('date');
	

class SearchDateForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('delovodnik');
        $this->setAttribute('method', 'post');

      $this->add(array(
			 'type' => 'Zend\Form\Element\Date',
			 'name' => 'search_date',
			 'options' => array(
					 'label' => 'Datum unosa'
			 ),
			 'attributes' => array(
			 		 'class' => 'my_input',
					 'min' => '1974-01-01',
					 'max' => '2020-01-01',
					 'step' => '1', // days; default step interval is 1 day
			 )
		 ));  
		
		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',	
                'value' => 'Traži',
                'id' => 'SubmitButtonCreateForm',
            ),
        )); 
    }
}