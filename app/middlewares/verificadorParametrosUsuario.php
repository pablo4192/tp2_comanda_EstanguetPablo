<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class VerificadorParametrosUsuario
{
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
            if(!array_key_exists("id_usuario", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro id_usuario")));
            }
            else
            {
                if($data['id_usuario'] != null)
                {
                    if(is_numeric($data['id_usuario']) && $data['id_usuario'] > 0)
                    {
                        $dataToken = Jwtoken::Verificar($token);

                        if($dataToken->puesto != "socio")
                        {
                            $response->getBody()->write(json_encode(array("Acceso denegado" => "el id no pertenece a un usuario con el puesto socio")));   
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
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar id del usuario a eliminar")));
                }
            }

        }
        return $response;

    }

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
            if(!array_key_exists("usuario", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro usuario")));
            }
            else
            {
                $dataJson = $data['usuario'];
                $usuario = json_decode($dataJson);
        
                if(isset($usuario->nombre) && isset($usuario->apellido) && isset($usuario->clave) && isset($usuario->puesto))
                {
                    if(ctype_alpha($usuario->nombre) && $usuario->nombre != "" && 
                       ctype_alpha($usuario->apellido) && $usuario->apellido != "" && 
                       $usuario->clave != "" && 
                       ctype_alpha($usuario->puesto) && $usuario->puesto != "" &&
                       ($usuario->puesto == "cocinero" || $usuario->puesto == "mozo" || $usuario->puesto == "bartender" || $usuario->puesto == "cervecero" || $usuario->puesto == "repostero" || $usuario->puesto == "socio"))
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo nombre, apellido, clave y puesto contenga datos, y esten corrrectos (nombre-apellido solo letras, puestos (bartender, cervecero, repostero, mozo, socio))")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar: nombre, apellido, clave, puesto del usuarios")));
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

            if($dataToken->puesto == "socio")
            {
                if(array_key_exists("nombre", $data))
                {
                    $dataJson = $data['nombre'];
                    $usuario = json_decode($dataJson);

                    if(isset($usuario->id) && isset($usuario->nombre) && $usuario->id > 0  && ctype_alpha($usuario->nombre) && $usuario->nombre != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y nombre contengan datos")));
                    }
                }
                else if(array_key_exists("apellido", $data)) 
                {
                    $dataJson = $data['apellido'];
                    $usuario = json_decode($dataJson);

                    if(isset($usuario->id) && isset($usuario->apellido) && $usuario->id > 0  && ctype_alpha($usuario->apellido) && $usuario->apellido != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y apellido contengan datos")));
                    }
                }
                else if(array_key_exists("clave", $data)) 
                {
                    $dataJson = $data['clave'];
                    $usuario = json_decode($dataJson);

                    if(isset($usuario->id) && isset($usuario->clave) && $usuario->id > 0  && is_numeric($usuario->clave) && $usuario->clave > 0)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y clave contengan datos")));
                    }
                }
                else if(array_key_exists("puesto", $data)) 
                {
                    $dataJson = $data['puesto'];
                    $usuario = json_decode($dataJson);

                    if(isset($usuario->id) && isset($usuario->puesto) && $usuario->id > 0  && ctype_alpha($usuario->puesto) && ($usuario->puesto == "cocinero" || $usuario->puesto == "repostero" || $usuario->puesto == "bartender" || $usuario->puesto == "cervecero" || $usuario->puesto == "socio"))
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y puesto contengan datos y sean validos")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro valido para modificacion")));
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("Acceso denegado" => "el id no pertenece a un usuario con el puesto socio")));
            }
    
        }
        return $response;
    }

    public static function VerificarLogin($request, $handler)
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
            if(!array_key_exists("usuario", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso el parametro usuario")));
            }
            else
            {
                $dataJson = $data['usuario'];
                $usuario = json_decode($dataJson);

                if(isset($usuario->id) && isset($usuario->clave))
                {
                    if(is_numeric($usuario->id) && $usuario->id > 0 && $usuario->clave != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br> Parametros verificados, metodo de consulta " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que los campos id y clave contengan datos")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros debe ingresar id y clave del usuario para loguearse")));
                }
            
            }
            
        }
        return $response;
            
    }
}

?>