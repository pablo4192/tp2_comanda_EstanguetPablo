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

    public static function VerificarFechasConsultas($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($data))
        {
            $payload = json_encode(array("Error" => "No ingreso parametro 'parametros'"));
            $response = $response->withStatus(400);
        }
        else
        {
            if(!array_key_exists("parametros", $data))
            {
                $payload = json_encode(array("Error" => "No ingreso parametro 'parametros'"));
                $response = $response->withStatus(400);
            }
            else
            {
                $params = json_decode($data['parametros']);

                if(!isset($params->desde) && !isset($params->hasta))
                {
                    $payload = json_encode(array("Error" => "Verifique parametros fechas: {'desde':'aaaa/mm/dd','hasta':'aaaa/mm/dd'}"));
                    $response = $response->withStatus(400);
                }
                else
                {
                    if(count(explode("/", $params->desde)) == 3 && count(explode("/", $params->hasta)) == 3)
                    {
                        $response = $handler->handle($request);
                        $response = $response->withStatus(200);
                        $payload = json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta ".$method));
                    }
                    else
                    {
                        $payload = json_encode(array("Error" => "No ingreso formato valido para las fechas (aaaa/mm/dd)"));
                        $response = $response->withStatus(400);
                    }

                }
            }
        }
        $response->getBody()->write($payload);
        return $response;

    }

    public static function VerificarConsultaOperaciones($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($data))
        {
            $payload = json_encode(array("Error" => "No ingreso parametro 'puesto'"));
            $response = $response->withStatus(400);
        }
        else
        {
            if(!array_key_exists("puesto", $data))
            {
                $payload = json_encode(array("Error" => "No ingreso parametro 'puesto'"));
                $response = $response->withStatus(400);
            }   
            else
            {
                if(!ctype_alpha($data['puesto']) || $data['puesto'] == "")
                {
                    $payload = json_encode(array("Error" => "Verifique parametros, puesto debe contener solo letras y el campo no puede estar vacio"));
                    $response = $response->withStatus(400);
                }
                else
                {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta ".$method));
                    $response = $response->withStatus(200);
                }
            }
        }
        $response->getBody()->write($payload);
        return $response;
    }

}

?>