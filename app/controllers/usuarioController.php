<?php
require_once "./models/usuario.php";

class UsuarioController
{
    public function AltaUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['usuario'];
        $usuario = json_decode($dataJson);

        $usuarioAInsertar = new Usuario();
        $usuarioAInsertar->nombre = $usuario->nombre;
        $usuarioAInsertar->apellido = $usuario->apellido;
        $usuarioAInsertar->clave = $usuario->clave;
        $usuarioAInsertar->puesto = $usuario->puesto;

        if(Usuario::Insertar($usuarioAInsertar))
        {
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
        $lista = Usuario::Listar();

        $payload = json_encode(array("ListaUsuarios" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }

    public function BajaUsuario($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $id_bajaUsuario = $data['id_usuario'];

        if(Usuario::EliminarUsuario($id_bajaUsuario))
        {
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
        
        if(Usuario::ModificarUsuario($usuarioAModificar))
        {
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
}

?>