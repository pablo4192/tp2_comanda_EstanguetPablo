<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class Verificador
{
    public static function VerificarParametrosArchivos($request, $handler)
    {
        $files = $request->getUploadedFiles();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($files))
        {
            $payload = json_encode(array("Error" => "No escogio archivo.csv para subir"));
        }
        else
        {
            if(!array_key_exists("archivo_csv", $files))
            {
                $payload = json_encode(array("Error" => "No envio el parametro archivo_csv"));
            }
            else
            {
                if($files['archivo_csv']->getSize() <= 0)
                {
                    $payload = json_encode(array("Error" => "No ingreso un archivo.csv"));
                }
                else
                {
                    $extension = explode(".", $files['archivo_csv']->getClientFilename())[1];
    
                    if($extension != "csv")
                    {
                        $payload = json_encode(array("Error" => "El archivo que intenta subir no es extension .csv"));
                    }
                    else
                    {
                        $response = $handler->handle($request);
                        $payload = json_encode(array("Mensaje" => "Parametros del archivo verficados"));
                    }

                }

            }

        }
        $response->getBody()->write($payload);
        
        return $response;

    }

}

?>