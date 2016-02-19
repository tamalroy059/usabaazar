<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Usabaazar\Model;

use Zend\Db\TableGateway\TableGateway;

Class UsabaazarTable{
    
    protected $tableGateway;
    
    public function _construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function saveRegistration(Usabaazar $usabaazar){
        $data = array (
            'artist' => $usabaazar->firstName,
            'title' => $usabaazar->lastName,
            'companyName' => $usabaazar->companyName,
            'email' => $usabaazar->email,
            'password' => $usabaazar->password,
            'address1' => $usabaazar->address1,
            'address2' => $usabaazar->address2,
            'countrySelect' => $usabaazar->countrySelect,
            'city' => $usabaazar->city,
            'areaCode' => $usabaazar->areaCode,
            'phoneNumber' => $usabaazar->phoneNumber,
        );
        
        $id = (int)$usabaazar->id;
        
        if($id==0){
            $this->tableGateway->insert($data);
        }
        else{
            echo "Data no entered";
        }
        
    }
    
}