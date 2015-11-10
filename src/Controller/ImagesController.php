<?php

namespace App\Controller;

class ImagesController extends AppController
{
    public function index()
    {
        $this->set('jsIncludes',array('angular-dragula.min'));
        $this->set('jsIncludes',array('drop'));
    }
}
