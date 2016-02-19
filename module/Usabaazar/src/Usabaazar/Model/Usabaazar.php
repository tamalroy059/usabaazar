<?php
namespace Usabaazar\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        


class Usabaazar implements InputFilterAwareInterface

{
    
    public $id;
    public $firstName;
    public $lastName;
    public $companyName;
    public $email;
    public $password;
    public $address1;
    public $address2;
    public $countrySelect;
    public $city;
    
    public $areaCode;
    public $phoneNumber;
    
    protected $inputFilter;

    
    public function exchangeArray($data){
        $this->id     = (isset($data['id']))     ? $data['id']     : null;
        
        $this->firstName     = (isset($data['firstName']))     ? $data['firstName']     : null;
        $this->lastName     = (isset($data['lastName']))     ? $data['lastName']     : null;
        $this->companyName     = (isset($data['companyName']))     ? $data['companyName']     : null;
        $this->email     = (isset($data['email']))     ? $data['email']     : null;
        $this->password     = (isset($data['password']))     ? $data['password']     : null;
        $this->address1     = (isset($data['address1']))     ? $data['address1']     : null;
        $this->address2     = (isset($data['address2']))     ? $data['address2']     : null;
        $this->countrySelect     = (isset($data['countrySelect']))     ? $data['countrySelect']     : null;
        $this->city     = (isset($data['city']))     ? $data['city']     : null;
        $this->areaCode     = (isset($data['areaCode']))     ? $data['areaCode']     : null;
        $this->phoneNumber     = (isset($data['phoneNumber']))     ? $data['phoneNumber']     : null;
        
    }
    
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter() {
        
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
            

            $inputFilter->add($factory->createInput(array(
                'name'     => 'firstName',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'lastName',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'companyName',
                
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'address1',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'address2',
                
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'countrySelect',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'city',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'areaCode',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'phoneNumber',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

}