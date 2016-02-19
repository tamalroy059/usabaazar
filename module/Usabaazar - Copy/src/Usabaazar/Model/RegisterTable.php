<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Usabaazar\Model;

use Zend\Db\TableGateway\TableGateway;

Class RegisterTable{
    
    protected $tableGateway;
    
    public function _construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function saveRegistration(RegisterModel $registerModel){
        $data = array (
            'firstName' => $registerModel->firstName,
            'lastName' => $registerModel->lastName,
            'companyName' => $registerModel->companyName,
            'email' => $registerModel->email,
            'password' => $registerModel->password,
            'address1' => $registerModel->address1,
            'address2' => $registerModel->address2,
            'countrySelect' => $registerModel->countrySelect,
            'city' => $registerModel->city,
            'areaCode' => $registerModel->areaCode,
            'phoneNumber' => $registerModel->phoneNumber,
        );
        
        $id = (int)$registerModel->id;
        print_r($data);
        if($id==0){
            $this->tableGateway->insert($data);
        }
        else{
            echo "Data no entered";
        }
        
    }
    
}