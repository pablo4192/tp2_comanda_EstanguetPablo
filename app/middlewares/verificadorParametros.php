<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

require_once "./models/jwt.php";

class VerificadorParametros
{
    public static function VerificarParametrosProductoAlta($request, $handler)
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

    public static function VerificarParametrosProductoBaja($request, $handler)
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

    public static function VerificarParametrosUsuarioBaja($request, $handler)
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

    public static function VerificarParametrosPedidoBaja($request, $handler)
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

    public static function VerificarParametrosMesaBaja($request, $handler)
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
                    if(is_numeric($data['id_mesa']) && $data['id_mesa'] > 0)
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
                        $response->getBody()->write(json_encode(array("Error" => "El id debe ser numerico")));
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


    public static function VerificarParametrosUsuario($request, $handler)
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
                    $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, debe ingresar: nombre, apellido, clave y puesto del usuario")));
                }
            }
        }
       
        return $response;
    }

    public static function VerificarParametrosPedido($request, $handler)  //Hacer verificacion de accion por e token
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
                $pedido = json_decode($dataJson);
        
                if(isset($pedido->productos) && isset($pedido->nombre_cliente) && isset($pedido->id_mesa))
                {
                    if( self::ExisteProducto($pedido->productos) &&
                        ctype_alpha($pedido->nombre_cliente) && 
                        is_numeric($pedido->id_mozo) && 
                        is_numeric($pedido->id_mesa) &&
                        $pedido->id_mesa > 0)
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

    public static function VerificarParametrosMesa($request, $handler)
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
                    if(is_numeric($mesa->id) && $mesa->id > 0) //hacer funcion para no repetir id_mesa
                    {
                        $response = $handler->handle($request);
                        $response->getBody()->write("<br>Parametros verificados, Metodo de consulta: " . $method);
                    }
                    else
                    {
                        $response->getBody()->write(json_encode(array("Error" => "Verifique parametros, el id de la mesa debe ser mayor a 0")));
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

    public static function VerificarParametrosLogin($request, $handler)
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

    public static function VerificarParametrosModificacionProducto($request, $handler)
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

    public static function VerificarParametrosModificacionUsuario($request, $handler)
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

    public static function VerificarParametrosModificacionPedido($request, $handler)
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

    public static function VerificarParametrosCambiosEstadoPedidos($request, $handler)
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

}

?>