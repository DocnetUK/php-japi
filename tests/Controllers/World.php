<?php
namespace Hello;
class World extends \Docnet\JAPI\Controller
{
    public function dispatch(){
        $this->setResponse([
            'input1' => $this->getQuery('input1'),
            'input2' => $this->getPost('input2'),
            'input3' => $this->getParam('input3'),
            'input4' => $this->getParam('input4')
        ]);
    }
}