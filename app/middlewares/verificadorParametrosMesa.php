<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class VerificadorParametrosMesa
{
    public static function VerificarAlta($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();
        
        if(!isset($data))
        {   
            $response->getBody()->write(json_encode(array("Error" => "No ingreso ningun parametro")));
        }
        else
        {
            if(!array_key_exists("mesa", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro mesa")));
            }
            else
            {
                $dataJson = $data['mesa'];
                $mesa = json_decode($dataJson);
        
                if(isset($mesa->id))
                {
                    if(!is_numeric($mesa->id) && strlen($mesa->id) == 5)
                    {
                        if(!Mesa::ExisteId($mesa->id))
                        {
                            $response = $handler->handle($request);
                            $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                        }
                        else
                        {
                            $response->getBody()->write(json_encode(array("Error" => "Id repetido, el id de la mesa ya se encuentra en la base de datos")));
                        }
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, el id de la mesa debe ser alfanumerico de 5 caracteres")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar: id de la mesa")));
                }
            }
        }
       
        return $response;
    }
    
    public static function VerificarBaja($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }


        if(!isset($data))
        {   
            $response->getBody()->write(json_encode(array("Error" => "No ingreso ningun parametro")));
        }
        else
        {
            if(!array_key_exists("id_mesa", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro id_mesa")));
            }
            else
            {
                if($data['id_mesa'] != null)
                {
                    if(!is_numeric($data['id_mesa']) && $data['id_mesa'] != "")
                    {
                        $dataToken = Jwtoken::Verificar($token);

                        
                        if($dataToken->puesto != "socio")
                        {
                            $response->getBody()->write(json_encode(array("Acceso denegado" => "el id no pertenece a un usuario con el puesto socio (puestos validos para dar de baja mesas)")));   
                        }
                        else
                        {
                            $response = $handler->handle($request);
                            $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                        }
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "El id debe ser alfanumerico de 5 caracteres")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar id de la mesa a eliminar")));
                }
            }

        }
        return $response;

    }

    public static function VerificarCambiosEstado($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($data))
        {   
            $response->getBody()->write(json_encode(array("Error" => "No ingreso ningun parametro")));
        }
        else
        {
            
            switch($method)
            {
                case "POST":
                    if(array_key_exists("entregar", $data))
                    {
                        $id = $data['entregar'];
                        

                        if(isset($id))
                        {
                            if(!is_numeric($id) && $id != "")
                            {
                                $response = $handler->handle($request);
                                $response->getBody()->write(json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta: " . $method)));   
                            }
                            else
                            {
                                $response->getBody()->write(json_encode(array("Error" => "Verifique parametros (entregar {'id':id})")));   
                            }
                        }
                        else
                        {
                            $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'id' de la mesa")));   
                        }
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'entregar'")));   
                    }
                    break;
                    case "PUT":
                        if(array_key_exists("cobrar", $data))
                        {
                            $id = $data['cobrar'];
                            

                            if(isset($id))
                            {
                                if(!is_numeric($id) && $id != "")
                                {
                                    $response = $handler->handle($request);
                                    $response->getBody()->write(json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta: " . $method)));   
                                }
                                else
                                {
                                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros (entregar {'id':id})")));   
                                }
                            }
                            else
                            {
                                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'id' de la mesa")));   
                            }
                        }
                        else
                        {
                            $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'cobrar'")));   
                        }
                        break;
                        case "DELETE":
                            if(array_key_exists("cerrar", $data))
                            {
                                $dataJson = $data['cerrar'];
                                $datos = json_decode($dataJson);

                                if(isset($datos->id) && isset($datos->medio_de_pago))
                                {
                                    if(!is_numeric($datos->id) && $datos->id != "" && ($datos->medio_de_pago == "efectivo" || $datos->medio_de_pago == "mp" || $datos->medio_de_pago == "debito" || $datos->medio_de_pago == "credito"))
                                    {
                                        $response = $handler->handle($request);
                                        $response->getBody()->write(json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta: " . $method)));   
                                    }
                                    else
                                    {
                                        $response->getBody()->write(json_encode(array("Error" => "Verifique parametros (cobrar {'id_mesa':id,'medio_de_pago':medio})")));   
                                    }
                                }
                                else
                                {
                                    $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'id' de la mesa o medio_de_pago")));   
                                }
                            }
                            else
                            {
                                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro 'cerrar'")));   
                            }
                            break;
            }
        }

        return $response;


    }
   
    public static function VerificarParametrosEncuesta($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($data))
        {   
            $response->getBody()->write(json_encode(array("Error" => "No ingreso ningun parametro")));
            $response = $response->withStatus(400);
        }
        else
        {
            if(array_key_exists("id_pedido", $data) && array_key_exists("id_mesa", $data)  && array_key_exists("mesa", $data) && array_key_exists("restaurant", $data) && array_key_exists("mozo", $data) && array_key_exists("cocinero", $data) && array_key_exists("comentarios", $data))
            {
                if(!is_numeric($data['id_pedido']) && $data['id_pedido'] != "" &&
                   !is_numeric($data['id_mesa']) && $data['id_mesa'] != "" &&
                   $data['mesa'] > 0 && $data['mesa'] < 11 &&
                   $data['restaurant'] > 0 && $data['restaurant'] < 11 &&
                   $data['mozo'] > 0 && $data['mozo'] < 11 &&
                   $data['cocinero'] > 0 && $data['cocinero'] < 11 &&
                   strlen($data['comentarios']) < 67)
                {
                    $response = $handler->handle($request);
                    $response->getBody()->write(json_encode(array("Mensaje" => "Parametros verificados")));
                    $response = $response->withStatus(200);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Debe ingresar id_pedido y id_mesa. Las puntuaciones deben ser menores o iguales a 10 y el campo comentarios no debe sobrepasar los 66 caracteres")));
                    $response = $response->withStatus(400);
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("Error" => "Para completar la encuesta debe ingresar id_pedido, id_mesa; dar puntuacion a: mesa, restaurant, mozo, cocinero y completar un breve texto con su experiencia")));
                $response = $response->withStatus(400);
            }
        }
        return $response;


    }
  
    
    
}

?>