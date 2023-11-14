<?php

class Controller{

    public function __construct(public Model $model)
    {

    }

     public function processRequest(string $method, string|null $id, string|null $sub): void{
        if($sub AND $id){
            $this->processCollectionSubRequest($method, $id, $sub);

        } elseif ($id) {
            $this->processResourceRequest($method, $id);
        }
        else{
            $this->processCollectionRequest($method);

        }
    }
    private function processResourceRequest(string $method, string $id): void
    {
        $record = $this->model->get($id);

        if(!$record){
            http_response_code(404);
            echo json_encode(["status"=> "error","message"=> "Record not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode(["status"=>'success',"data"=>$record], JSON_UNESCAPED_UNICODE);
                break;
            case "PATCH":
                  $data =(array) json_decode(file_get_contents('php://input'));

                $error = $this->getValidationErrors($data, false);
                if(!empty($error)){
                    http_response_code(422);
                    echo json_encode(['error'=>$error]);
                    break;
                }
                $row = $this->model->update($record,$data);

                echo json_encode([
                    "message" => "Product $id updated",
                    "id" => $row
                ]);
                break;
            case "DELETE":
               $rows = $this->model->delete($id);

               echo json_encode([
                "message"=> "Product $id deleted",
                "rows"=> $rows
                ]);
                break; 
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
            

        }


    }

    private function processCollectionRequest(string $method): void
    {
        
        switch ($method) {
            case "GET":
                $data = $this->model->getAll();
                $this->emptyData($data);
                
                echo json_encode([
                    "status"=> "success",
                    "data"=> $data
                ]);
                break;
            case "POST":
                $data =(array) $_POST;
                $error = $this->getValidationErrors($data);
                if(!empty($error)){
                    http_response_code(422);
                    echo json_encode(['error'=>$error]);
                    break;
                }
              $newData = $this->model->insert($data);
              http_response_code(201);

                echo json_encode([
                    "message" => "Record created",
                    "data" => $newData
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }

    }

    private function processCollectionSubRequest(string $method, string $id, string $sub): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->model->$sub($id));
                break;
            default:
                http_response_code(405);
                header("Allow: GET");
        }

    }

     private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        // if ($is_new && empty($data["name"])) {
        //     $errors["name"] = "name is required";
        // }
        // if (array_key_exists("size", $data)) {
        //     if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
        //         $errors["size"] = 'size must be an integer';
        //     }
        // }
        return $errors;
    }

    private function emptyData(array $data)
    {
          if(empty($data)){
                    http_response_code(204);
                    echo json_encode([
                    "status"=> "success",
                    "data"=> $data
                ]);
            exit();
         }

    }
    
}