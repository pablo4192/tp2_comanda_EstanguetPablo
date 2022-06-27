<?php
require_once "./models/jwt.php";
//require_once "./models/usuario.php";

class LoggerController
{
    public function Loguear($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['usuario'];
        $usuario = json_decode($dataJson);

        $puesto = Usuario::VerificarPuesto($usuario->id, $usuario->clave);
        
        if($puesto != null)
        {
            $datosToken = json_encode(array('id' => $usuario->id, 'clave' => $usuario->clave, 'puesto' => $puesto));
        
            $token = Jwtoken::CrearToken($datosToken);

            if(Usuario::RegistrarLogin($usuario->id))
            {
                $response->getBody()->write(json_encode(array("Mensaje" => "Datos Login archivado en la base de datos")));
            }
            else
            {
                $response->getBody()->write(json_encode(array("Error" => "Datos Login NO archivados en la base de datos")));
            }

            $payload = json_encode(array('token' => $token));
            $response = $response->withStatus(200);

        }
        else
        {
            $payload = json_encode(array("Error" => "Usuario no encontrado en la base de datos"));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);

        return $response;
    }



}


?>