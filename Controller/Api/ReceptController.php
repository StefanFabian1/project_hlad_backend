<?php
class ReceptController extends BaseController{

    private ReceptDAO $receptDAO;
    

    public function __construct()
    {
        $this->receptDAO = new ReceptDAO();
        parent::__construct();
    }

    public function doGet()
    {
        $uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if (isset($uri[3])) {
            if (ctype_digit($uri[3]) && (int)$uri[3] >0) {
                $receptId = (int)$uri[3];
                $this->getSingleRecipe($receptId);
            } else {
                $this->sendErrorResponse('Page Not Found', 404);
            }
        } else {
            $this->getAllRecipes();
        }
    }

    private function getSingleRecipe(int $receptId) : void {
        try {
            $result = $this->receptDAO->find($this->userId, $receptId);
            if ($result != null && !empty($result) && $result->getId() != null) {
                $this->sendSuccessResponse(array($result), 200);
            } else if ($result != null && !empty($result) && $result->getId() == null) {
                $this->sendErrorResponse('Unauthorized access', 401);
            } else {
                $this->sendNoContentResponse();
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while getting recipe: ' . $e->getMessage(), 500);
        }
    }

    private function getAllRecipes() : void {
        try {
            $result = $this->receptDAO->findAll($this->userId);
            if ($result != null && !empty($result)) {
                $this->sendSuccessResponse($result, 200);
            } else {
                $this->sendNoContentResponse();
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while getting recipes: ' . $e->getMessage(), 500);
        }
    }

    public function doPost()
    {
        echo ("post - posleme nejake filtre z FE :D");
    }

    public function doPut() {
        //TODO handle sucess and failed...
        $this->receptDAO->saveRecept($this->getJsonAsArray());
    }

    public function doPatch()
    {
        echo ("patch - budeme posielat len atributy ktore sa zmenili, teda dotiahnem cely objekt a upravim poz atribut");
    }
}
?>