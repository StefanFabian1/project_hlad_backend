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
        try {
            $receptId = $this->receptDAO->saveRecept($this->getJsonAsArray(), $this->userId);
            if ($receptId > 0) {
                $this->sendSuccessResponse(array($receptId), 200);
            } else {
                $this->sendErrorResponse('Nepodarilo sa ulozit novy recept', 500);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while saving recipe: ' . $e->getMessage(), 500);
        }
    }

    public function doPatch()
    {
        $uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if (isset($uri[3]) && ctype_digit($uri[3]) && (int)$uri[3] > 0) {
            try {
                $receptId = (int)$uri[3];
                $result = $this->receptDAO->updateRecept($this->getJsonAsArray(), $receptId, $this->userId);
                if ($result === null) {
                    $this->sendErrorResponse('Unauthorized access', 401);
                } else if ($result > 0) {
                    $this->sendSuccessResponse(array(''), 200);
                } else {
                    $this->sendNoContentResponse();
                }
            } catch (Exception $e) {
                $this->sendErrorResponse('Exception occured while updating recipe: ' . $e->getMessage(), 500);
            }
        } else {
            $this->sendErrorResponse('Page Not Found', 404);
        }
    }

    public function doDelete()
    {
        $uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if (isset($uri[3]) && ctype_digit($uri[3]) && (int)$uri[3] > 0) {
            try {
                $receptId = (int)$uri[3];
                $result = $this->receptDAO->deleteRecept($receptId, $this->userId);
                var_dump($result);
                if ($result === null) {
                    $this->sendErrorResponse('Unauthorized access', 401);
                } else if ($result > 0) {
                    $this->sendSuccessResponse(array(''), 200);
                } else {
                    $this->sendNoContentResponse();
                }
            } catch (Exception $e) {
                $this->sendErrorResponse('Exception occured while deleting recipe: ' . $e->getMessage(), 500);
            } 
        } else {
            $this->sendErrorResponse('Page Not Found', 404);
        }
    }
}
?>