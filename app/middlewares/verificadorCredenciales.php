<?php
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;


require_once "./models/jwt.php";
require_once "./models/usuario.php";


class VerificadorCredenciales
{
    public static function VerificarUsuario($request, $handler)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['usuario'];
        $usuario = json_decode($dataJson);
        $response = new Response();

        if(!Usuario::Existe($usuario))
        {
            $payload = json_encode(array("Acceso denegado" => "El usuario no se encuentra en la base de datos"));
            $response = $response->withStatus(400);
        }
        else
        {
            $dataUsuario = Usuario::RetornarDatosUsuario($usuario->id);
            $ruta = $request->getUri()->getPath();

            if($ruta != "/login" && $dataUsuario->estado == "suspendido")
            {
                $payload = json_encode(array("Acceso denegado" => "El usuario no puede ingresar al sistema, su estado es 'supendido'"));
                $response = $response->withStatus(400);
            }
            else
            {
                $response = $handler->handle($request); 
                $payload = json_encode(array("Bienvenido" => $dataUsuario['nombre']));
                
            }
        }
            
        $response->getBody()->write($payload);
        
        return $response;
    }

    public static function VerificarToken($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $response = new Response();
        
        $dataToken = Jwtoken::Verificar($token);


        if(!isset($dataToken->puesto))
        {
            $response->getBody()->write(json_encode(array("Mensaje" => "No posee Token, debe loguearse!!")));
        }
        else
        {
            if(!Usuario::Existe($dataToken))
            {
                $response->getBody()->write(json_encode(array("Error" => "El token es invalido, el usuario no existe en la base de datos!!")));
            }
            else
            {
                $ruta = $request->getUri()->getPath();
                
                if(self::VerificadorDeAccesos($ruta, $dataToken->puesto))
                {
                    $response = $handler->handle($request);
                    $response->getBody()->write("<br>");
                    $response->getBody()->write(json_encode(array("Mensaje" => "Token verificado")));
                }
                else
                {
                    $response->getBody()->write(json_encode(array("Mensaje" => "No posee autorizacion para realizar esta accion (token invalido)")));
                }
            }

        }

        return $response;
    }

    private static function VerificadorDeAccesos($ruta, $puesto)
    {
        $retorno;

        
        switch(trim($puesto))
        {
            case "socio":
                $retorno = true;    
                break;
                case "mozo":
                    if($ruta == "/pedidos" || $ruta == "/pedidos/baja" || $ruta == "/pedidos/modificacion" || $ruta == "/pedidos/listos" || $ruta == "/mesas/cambiosEstado" || $ruta == "/pedidos/cancelar")
                    {
                        $retorno = true;
                    }
                    else
                    {
                        $retorno = false;
                    }
                    break;
                    case "cocinero":
                        if($ruta == "/productos_pedidos/pendientes")
                        {
                            $retorno = true;
                        }
                        else
                        {
                            $retorno = false;
                        }
                        break;
                        case "cervecero":
                            if($ruta == "/productos_pedidos/pendientes")
                            {
                                $retorno = true;
                            }
                            else
                            {
                                $retorno = false;
                            }
                            break;
                            case "bartender":
                                if($ruta == "/productos_pedidos/pendientes")
                                {
                                    $retorno = true;
                                }
                                else
                                {
                                    $retorno = false;
                                }
                                break;
                                case "repostero":
                                if($ruta == "/productos_pedidos/pendientes")
                                {
                                    $retorno = true;
                                }
                                else
                                {
                                    $retorno = false;
                                }
                                break;

        }
        return $retorno;

    }
}

?>