<?php
class TestController extends BaseController {
   
    private TestModel $testModel;
    //TODO nebude init ale metodu vyberieme v konstruktore
    public function __construct()
    {
        $this->testModel = new TestModel();
        parent::chooseHttpRequestMethod();
    } 

    public function doGet() {
       $arrayTest = $this->testModel->getTestData();
       //TODO ohandlovat errors
       $this->sendOutput(json_encode($arrayTest), array('Content-Type: application/json', http_response_code(200)));
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