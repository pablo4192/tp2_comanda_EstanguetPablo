<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class VerificadorParametrosProducto
{
    public static function VerificarAlta($request, $handler)
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
            if(!array_key_exists("producto", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro producto")));
            }
            else
            {
                $dataJson = $data['producto'];
                $producto = json_decode($dataJson);
        
                if(isset($producto->nombre) && isset($producto->precio) && isset($producto->stock) && isset($producto->tipo) && isset($producto->tiempo_preparacion))
                {
                    if(ctype_alpha($producto->nombre) && $producto->nombre != "" && 
                       is_numeric($producto->precio) && $producto->precio > 0 && 
                       is_numeric($producto->stock) && $producto->stock > 0 && 
                       ctype_alpha($producto->tipo) && $producto->tipo != "" && ($producto->tipo == "cocinero" || $producto->tipo == "repostero" || $producto->tipo == "bartender" || $producto->tipo == "cervecero") &&
                       is_numeric($producto->tiempo_preparacion) && $producto->tiempo_preparacion > 0)
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
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo nombre contenga datos, los campos precio, stock y tiempo_preparacion sean mayores a 0 y tipo sea valido (platoPrincipal, postre, bebida, cerveza)")));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar: nombre, precio, stock y tiempo_preparacion del producto")));
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
            if(!array_key_exists("id_producto", $data))
            {
                $response->getBody()->write(json_encode(array("Error" => "No ingreso parametro id_producto")));
            }
            else
            {
                if($data['id_producto'] != null)
                {
                    if(is_numeric($data['id_producto']) && $data['id_producto'] > 0)
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
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar id del producto a eliminar")));
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
                    $producto = json_decode($dataJson);

                    if(isset($producto->id) && isset($producto->nombre) && $producto->id > 0  && ctype_alpha($producto->nombre) && $producto->nombre != "")
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y nombre contengan datos")));
                    }
                }
                else if(array_key_exists("precio", $data)) 
                {
                    $dataJson = $data['precio'];
                    $producto = json_decode($dataJson);

                    if(isset($producto->id) && isset($producto->precio) && $producto->id > 0  && is_numeric($producto->precio) && $producto->precio > 0)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y precio contengan datos")));
                    }
                }
                else if(array_key_exists("stock", $data)) 
                {
                    $dataJson = $data['stock'];
                    $producto = json_decode($dataJson);

                    if(isset($producto->id) && isset($producto->stock) && $producto->id > 0  && is_numeric($producto->stock) && $producto->stock > 0)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y stock contengan datos")));
                    }
                }
                else if(array_key_exists("tipo", $data)) 
                {
                    $dataJson = $data['tipo'];
                    $producto = json_decode($dataJson);

                    if(isset($producto->id) && isset($producto->tipo) && $producto->id > 0  && ctype_alpha($producto->tipo) && ($producto->tipo == "cocinero" || $producto->tipo == "repostero" || $producto->tipo == "bartender" || $producto->tipo == "cervecero"))
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y tipo contengan datos y sean validos")));
                    }
                }
                else if(array_key_exists("tiempo_preparacion", $data)) 
                {
                    $dataJson = $data['tiempo_preparacion'];
                    $producto = json_decode($dataJson);

                    if(isset($producto->id) && isset($producto->tiempo_preparacion) && $producto->id > 0  && is_numeric($producto->tiempo_preparacion) && $producto->tiempo_preparacion > 0)
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique que el campo id y tiempo_preparacion contengan datos")));
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

    private static function ExisteProducto($productos)
    {
        for($i = 0; $i < count($productos); $i++)
        {
            if(!Producto::Existe($productos[$i]))
            {
                return false;
            }
        }
        return true;

    }

}

?>