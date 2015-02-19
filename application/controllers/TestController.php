<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function arrayAction()
    {
        // action body
    }

    public function objectAction()
    {
        // action body
        $q=1;
        
    }

    public function equlsAction()
    {
        // action body
    }

    public function optionalparamsAction()
    {
        global $application;
        $bootstrap = $application->getBootstrap();
        $dic = $bootstrap->getResource('Dic');
        $dic->get('serviceWithOptionalParams' , 'Zaboy_Example_Service_OptionalParams');
    }

    public function countinterfaceAction()
    {
        // action body
    }


}

























