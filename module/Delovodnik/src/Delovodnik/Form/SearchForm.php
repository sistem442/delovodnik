<?php
namespace Delovodnik\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SearchForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('search');
        $this->setAttribute('method', 'post');
		
		$this->add(array(
			 'type' => 'Zend\Form\Element\Date',
			 'name' => 'date_of_entry_start',
			 'options' => array(
					 'label' => 'Datum unosa početni'
			 ),
			 'attributes' => array(
			 		 'class' => 'my_input',
					 'min' => '1974-01-01',
					 'max' => '2020-01-01',
					 'step' => '1', // days; default step interval is 1 day
			 )
		 ));  
		$this->add(array(
			 'type' => 'Zend\Form\Element\Date',
			 'name' => 'date_of_entry_end',
			 'options' => array(
					 'label' => 'Datum unosa krajnji'
			 ),
			 'attributes' => array(
			 		 'class' => 'my_input',
					 'min' => '1974-01-01',
					 'max' => '2020-01-01',
					 'step' => '1', // days; default step interval is 1 day
			 )
		 ));  

       $this->add(array(
            'name' => 'doc_id',
            'attributes' => array(
                'type'  => 'text',
				'class' => 'my_input',
            ),
            'options' => array(
                'label' => 'Delovodni broj',
            ),
        ));
		$this->add(array(
            'name' => 'doc_title',
            'attributes' => array(
                'type'  => 'textarea',
				'class' => 'my_input',
				'rows' => '5',
				'cols' => '30',
            ),
            'options' => array(
                'label' => 'Predmet',
            ),
        ));
		$this->add(array(
            'name' => 'company',
            'attributes' => array(
                'type'  => 'text',
				'class' => 'my_input',
            ),
            'options' => array(
                'label' => 'Firma ili lice',
            ),
        ));
		$this->add(array(
            'name' => 'doc_company_id',
            'attributes' => array(
                'type'  => 'text',
				'class' => 'my_input',
            ),
            'options' => array(
                'label' => 'Njihov broj',
            ),
        ));
		
		$this->add(array(
			 'type' => 'Zend\Form\Element\Date',
			 'name' => 'date_start',
			 'options' => array(
					 'label' => 'Datum na dokumentu početni'
			 ),
			 'attributes' => array(
			 		 'class' => 'my_input',
					 //'min' => '2012-01-01',
					 //'max' => '2020-01-01',
					 //'step' => '1', // days; default step interval is 1 day
			 )
		 ));
		$this->add(array(
			 'type' => 'Zend\Form\Element\Date',
			 'name' => 'date_end',
			 'options' => array(
					 'label' => 'Datum na dokumentu krajnji'
			 ),
			 'attributes' => array(
			 		 'class' => 'my_input',
					 //'min' => '2012-01-01',
					 //'max' => '2020-01-01',
					 //'step' => '1', // days; default step interval is 1 day
			 )
		 ));
		 
		$this->add(array(
            'name' => 'doc_location',
            'attributes' => array(
                'type'  => 'text',
				'class' => 'my_input',
            ),
            'options' => array(
                'label' => 'Lokacija dokumenta',
            ),
        ));
		$this->add(array(
            'name' => 'remark',
            'attributes' => array(
                'type'  => 'text',
				'class' => 'my_input',
            ),
            'options' => array(
                'label' => 'Napomena',
            ),
        ));
		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',	
                'value' => 'Traži',
                'id' => 'SubmitButtonSearchForm',
            ),
        )); 
	}
}