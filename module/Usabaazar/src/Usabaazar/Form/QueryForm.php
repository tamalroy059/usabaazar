<?php
namespace Usabaazar\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class QueryForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('queryForm');
        //$this->setAttribute('method', 'get');
        	
        
        $searchQuery = new Element\Text('searchQuery');
	$searchQuery
		->setAttributes(array(
			'autofocus'  => 'autofocus',
                        'id' => 'search',
                        'placeholder' => 'Search Anything',
                        'required' => 'required',
		));
			
	$this->add($searchQuery);
        	

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'search',
                'id' =>'submit',
            ),
        ));
    }
}