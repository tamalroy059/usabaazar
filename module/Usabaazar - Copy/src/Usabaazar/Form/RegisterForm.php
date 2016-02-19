<?php
namespace Usabaazar\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class RegisterForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('RegisterForm');
        $this->setAttribute('method', 'post');
        	
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        
        $firstName = new Element\Text('firstName');
	$firstName
		->setAttributes(array(
			"name" => "firstName",
                        "onblur"=>"if (this.value == '') {this.value = 'First Name';}",
                        'placeholder' => 'First Name',
//                        'required' => 'required',
                           
		));
			
	$this->add($firstName);
        	
        
        $lastName = new Element\Text('lastName');
	$lastName
		->setAttributes(array(
			"name" => "lastName",
                        "onblur"=>"if (this.value == '') {this.value = 'Last Name';}",
                        'placeholder' => 'Last Name',
//                        'required' => 'required',
                        
		));
			
	$this->add($lastName);
        
        
        $companyName = new Element\Text('companyName');
	$companyName
		->setAttributes(array(
			"name" => "companyName",
                        "onblur"=>"if (this.value == '') {this.value = 'Company Name (Optional)';}",
                        'placeholder' => 'Company Name (Optional)',
                        
		));
			
	$this->add($companyName);
        
        
        $email = new Element\Text('email');
	$email
		->setAttributes(array(
                        "value" => "email",
			"name" => "email",
                        "onblur"=>"if (this.value == '') {this.value = 'E-mail';}",
                        'placeholder' => 'E-Mail',
//                        'required' => 'required',
		));
			
	$this->add($email);
        
        
        $password = new Element\Password('password');
        $password
                ->setAttributes(array(
                    "value" => "Password",
                    "name" => "password",
                    "onblur"=>"if (this.value == '') {this.value = 'Password';}",
                    'placeholder' => 'Password',
//                    'required' => 'required',
                    'id' => 'reg-pass',
                    
                ));
        
        $this->add($password);
        
        
        $address1 = new Element\Text('address1');
	$address1
		->setAttributes(array(
                        "value" => "Address Field 1",
			"name" => "address1",
                        "onblur"=>"if (this.value == '') {this.value = 'Address Field 1';}",
                        'placeholder' => 'Address Field 1',
//                        'required' => 'required',
		));
			
	$this->add($address1);
        
        
        $address2 = new Element\Text('address2');
	$address2
		->setAttributes(array(
                        "name" => "address2",
                        "value" => "Address Field 2 (Optional)",
			"onfocus"=>"this.value='';",
                        "onblur"=>"if (this.value == '') {this.value = 'Address Field 2 (Optional)';}",
                        'placeholder' => 'Address Field 1 (Optional)',
                        
		));
			
	$this->add($address2);
        
        
        $countrySelect = new Element\Select('countrySelect');
        
        $countrySelect->setValueOptions(array(
             'null' => 'Select A Country',
             'AX' => 'Åland Islands',
             'US' => 'United States',
             'CA' => 'Canada',
     ));
        
        $this->add($countrySelect);
        
        
        $city = new Element\Text('city');
	$city
		->setAttributes(array(
                        "value" => "City",
			
                        "onblur"=>"if (this.value == '') {this.value = 'City';}",
                        'placeholder' => 'City',
                        'required' => 'required',
		));
			
	$this->add($city);
        
        
        $areaCode = new Element\Text('areaCode');
	$areaCode
		->setAttributes(array(
                        "class" => "code",
                        "pattern"=>"[0-9]*",
//			'required' => 'required',
		));
			
	$this->add($areaCode);
        
        $phoneNumber = new Element\Text('phoneNumber');
	$phoneNumber
		->setAttributes(array(
                        "class" => "number",
//                        'required' => 'required',
//                        "pattern"=>"^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$",
		));
			
	$this->add($phoneNumber);
        
       

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'class' => 'grey',
                'type'  => 'button',
                'value' => 'submit',
                'id' =>'reg123',
            ),
        ));
    }
}