<?php
require_once "./models/usuario.php";

class UsuarioController
{
    public function AltaUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['usuario'];
        $usuario = json_decode($dataJson);

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);

        $usuarioAInsertar = new Usuario();
        $usuarioAInsertar->nombre = $usuario->nombre;
        $usuarioAInsertar->apellido = $usuario->apellido;
        $usuarioAInsertar->clave = $usuario->clave;
        $usuarioAInsertar->puesto = $usuario->puesto;

        if(Usuario::Insertar($usuarioAInsertar))
        {
            Usuario::RegistrarOperacion($dataToken, "alta usuario");
            $payload = json_encode(array("mensaje" => "Usuario insertado con exito en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Hubo un problema al insertar el usuario en la base de datos"));
            $response = $response->withStatus(400);
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ListarUsuarios($request, $response, $args)
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

        $dataToken = Jwtoken::Verificar($token);

        $lista = Usuario::Listar();
        Usuario::RegistrarOperacion($dataToken, "listado usuarios");

        $payload = json_encode(array("ListaUsuarios" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }

    public function BajaUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $id_bajaUsuario = $data['id_usuario'];

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);

        if(Usuario::EliminarUsuario($id_bajaUsuario))
        {
            Usuario::RegistrarOperacion($dataToken, "baja usuario");
            
            $payload = json_encode(array("Mensaje" => "El usuario a sido eliminado de la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El usuario NO a sido eliminado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ModificarUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        if(array_key_exists("nombre", $data))
        {
            $usuarioAModificar = json_decode($data['nombre']);
        }
        else if(array_key_exists("apellido", $data))
        {
            $usuarioAModificar = json_decode($data['apellido']);
        }
        else if(array_key_exists("clave", $data))
        {
            $usuarioAModificar = json_decode($data['clave']);
        }
        else if(array_key_exists("puesto", $data))
        {
            $usuarioAModificar = json_decode($data['puesto']);
        }

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);
        
        if(Usuario::ModificarUsuario($usuarioAModificar))
        {
            Usuario::RegistrarOperacion($dataToken, "modificacion usuario");

            $payload = json_encode(array("Mensaje" => "El usuario a sido modificado en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El usuario NO a sido modificado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function SuspenderUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if(isset($data) && array_key_exists("id_usuario", $data))
        {
            $id_usuario = $data['id_usuario'];
        }
        else
        {
            $id_usuario = 0;
        }

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);

        if(Usuario::Suspender($id_usuario))
        {
            Usuario::RegistrarOperacion($dataToken, "suspension del usuario id: ".$id_usuario);

            $payload = json_encode(array("Mensaje" => "El usuario id ".$id_usuario." fue suspendido"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El usuario ".$id_usuario." NO a sido suspendido. Verifique que el id_usuario exista en la base de datos"));
            $response = $response->withStatus(400); 
        }

        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function LevantarSuspensionUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if(isset($data) && array_key_exists("id_usuario", $data))
        {
            $id_usuario = $data['id_usuario'];
        }
        else
        {
            $id_usuario = 0;
        }

        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);

        if(Usuario::LevantarSuspension($id_usuario))
        {
            Usuario::RegistrarOperacion($dataToken, "se levanto la suspension del usuario id: ".$id_usuario);

            $payload = json_encode(array("Mensaje" => "Al usuario id ".$id_usuario." se le levanto la suspension, ya puede operar normalmente"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "Al usuario ".$id_usuario." NO se le a levantado la suspension. Verifique que el id_usuario exista en la base de datos y que no se haya levantado la suspension anteriormente"));
            $response = $response->withStatus(400); 
        }
        
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}

?>