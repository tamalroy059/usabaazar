<?php

namespace Usabaazar;

 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;
 use Zend\Mvc\MvcEvent;

 //insert
 
 use Usabaazar\Model\RegisterModel;
 use Usabaazar\Model\RegisterTable;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;
 
 
 
 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
     
     
     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Usabaazar\Model\RegisterTable' =>  function($sm) {
                     $tableGateway = $sm->get('RegisterTableGateway');
                     $table = new RegisterTable($tableGateway);
                     return $table;
                 },
                 'RegisterTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new RegisterModel());
                     return new TableGateway('register', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
     
     
     public function onBootstrap(MvcEvent $e)
    {
        $em = $e->getApplication()->getEventManager();
        $em->attach('route', array($this, 'checkAuthenticated'));
    }

    public function isOpenRequest(MvcEvent $e)
    {
        if ($e->getRouteMatch()->getParam('controller') == 'Usabaazar\Controller\AuthController') {
            return true;
        }

        return false;
    }

    public function checkAuthenticated(MvcEvent $e)
    {
    }
    
 }