<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class VerificadorParametrosPedido
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
            if(!array_key_exists("pedido", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro pedido")));
            }
            else
            {
                $dataJson = $data['pedido'];
                $pedido = json_decode(($dataJson));
        
               
                if(isset($pedido->productos) && isset($pedido->nombre_cliente) && isset($pedido->id_mesa))
                {
                    if(VerificadorParametrosProducto::ExisteProducto($pedido->productos) &&
                        ctype_alpha($pedido->nombre_cliente) && 
                        is_numeric($pedido->id_mozo) && 
                        !is_numeric($pedido->id_mesa))
                    {
                        if(!Mesa::EstaDisponible($pedido->id_mesa))
                        {
                            $response->getBody()->write(json_encode(array("Error" => "La mesa no esta disponible para tomar el pedido")));
                        }
                        else
                        {
                            
                            if(!Usuario::EsMozo($pedido->id_mozo))
                            {
                                $response->getBody()->write(json_encode(array("Acceso denegado" => "id_mozo no valido. No se encontro un mozo con el id ingresado")));
                            }
                            else
                            {
                                $response = $handler->handle($request);
                                $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);

                            }

                        }

                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo productos, nombre_cliente e id_mesa tengan datos; el campo productos debe tener productos existentes en la base de datos")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar: productos (nombre,cantidad), nombre_cliente, id_mesa del pedido")));
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
            if(!array_key_exists("id_pedido", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro id_pedido")));
            }
            else
            {
                if($data['id_pedido'] != null)
                {
                    if(is_numeric($data['id_pedido']) && $data['id_pedido'] > 0)
                    {
                        $dataToken = Jwtoken::Verificar($token);

                        
                        if($dataToken->puesto != "socio" && $dataToken->puesto != "mozo")
                        {
                            $response->getBody()->write(json_encode(array("Acceso denegado" => "el id no pertenece a un usuario con el puesto socio o mozo (puestos validos para dar de baja pedidos)")));   
                        }
                        else
                        {
                            $response = $handler->handle($request);
                            $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                        }
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "El id debe ser numerico")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar id del pedido a eliminar")));
                }
            }

        }
        return $response;

    }

    public static function VerificarModificacion($request, $handler)
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
            $dataToken = Jwtoken::Verificar($token);

            if($dataToken->puesto == "socio" || $dataToken->puesto == "mozo")
            {
                if(array_key_exists("nombre_cliente", $data))
                {
                    $dataJson = $data['nombre_cliente'];
                    $pedido = json_decode($dataJson);

                    if(isset($pedido->id) && isset($pedido->nombre_cliente) && !isset($pedido->id_mesa) && $pedido->id > 0  && ctype_alpha($pedido->nombre_ciente) && $pedido->nombre_cliente != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y nombre_cliente contengan datos")));
                    }
                }
                else if(array_key_exists("id_mesa", $data)) 
                {
                    $dataJson = $data['id_mesa'];
                    $pedido = json_decode($dataJson);

                    if(isset($pedido->id) && isset($pedido->nombre_cliente) && isset($pedido->id_mesa) && $pedido->id > 0 && $pedido->id_mesa > 0 && ctype_alpha($pedido->nombre_cliente) && $pedido->nombre_cliente != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id, nombre_cliente e id_mesa contengan datos")));
                    }
                }
                else if(array_key_exists("productos", $data)) 
                {
                    $dataJson = $data['productos'];
                    $pedido = json_decode($dataJson);

                    if(isset($pedido->id) && isset($pedido->productos) && $pedido->id > 0)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y productos contengan datos")));
                    }
                }
                else if(array_key_exists("estado", $data))
                {
                    $response->getBody()->write(json_encode(array("mensaje" => "No ingreso parametro valido para modificacion, El estado solo lo cambia el encargado de la preparacion")));
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro valido para modificacion")));
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("Acceso denegado" => "el id no pertenece a un usuario con el puesto socio o mozo (validos para reaizar la modificacion del pedido)")));
            }
    
        }
        return $response;
    }

    public static function VerificarCambiosEstado($request, $handler)
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
            if(array_key_exists("estado", $data))
            {
                $dataJson = $data['estado'];
                $producto = json_decode($dataJson);
                
                
                if(isset($producto->id_pedido) && $producto->id_pedido > 0 && isset($producto->estado) && ($producto->estado == "listo" || $producto->estado == "en preparacion"))
                {
                    $dataToken = Jwtoken::Verificar($token);
                    
                    if(Pedido::Existe($producto->id_pedido))
                    {
                        if(Pedido::VerificarEstadoEnDB($producto, $dataToken->puesto))
                        {
                            $response = $handler->handle($request);
                            $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                        }
                        else
                        {
                            $response->getBody()->write(json_encode(array("Error" => "Accion invalida en cambios de estado del pedido, esta pasando por alto un estado")));
                        }
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "El id_pedido ingresado no se encuentra en la base de datos")));
                    }     

                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar {'id_pedido':id,'estado':estado}")));
                }

            }
            else
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro estado")));
            }

        }
        return $response;


    }

    public static function VerificarTiempoEspera($request, $handler)
    {
        $data = $request->getParsedBody();
        $method = $request->getMethod();
        $response = new Response();

        if(!isset($data))
        {   
            $response->getBody()->write(json_encode(array("Error" => "No ingreso ningun parametro")));
            $response->withStatus(400);
        }
        else
        {
            if(array_key_exists("info", $data))
            {
                $dataJson = $data['info'];
                $info = json_decode($dataJson);

                if(isset($info->id_pedido) && isset($info->id_mesa))
                {
                    if(!is_numeric($info->id_pedido) && strlen($info->id_pedido) == 5 && !is_numeric($info->id_mesa) && strlen($info->id_mesa) == 5)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write(json_encode(array("Mensaje" => "Parametros verificados, metodo de consulta ".$method)));
                        $response = $response->withStatus(200);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, id_pedido y id_mesa deben ser alfanumericos de 5 caracteres")));
                        $response = $response->withStatus(400);
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingreasr id_pedido y id_mesa para ver el tiempo de espera de su pedido")));
                    $response = $response->withStatus(400);
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso el parametro info")));
                $response = $response->withStatus(400);
            }
        }
        return $response;
    }

}




?>

