<?php
class TestController extends BaseController {
   
    //TODO nebude init ale metodu vyberieme v konstruktore
    public function __construct()
    {
        parent::chooseHttpRequestMethod();
    } 

    public function doGet() {
        echo ("get");
    }

    public function doPost()
    {
        echo ("post");
    }

    public function doPatch()
    {
        echo ("patch");
    }
}
?>