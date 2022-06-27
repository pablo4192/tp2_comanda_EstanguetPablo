<?php

class MesaController
{
    public function AltaMesa($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['mesa'];
        $mesa = json_decode($dataJson);

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
        
        $mesaAInsertar = new Mesa();
        $mesaAInsertar->id = $mesa->id;
        $mesaAInsertar->id_pedido = ""; 
        $mesaAInsertar->nombre_cliente = "";
        $mesaAInsertar->estado = "cerrada";
        if(Mesa::Insertar($mesaAInsertar))
        {
            Usuario::RegistrarOperacion($dataToken, "alta mesa");

            $payload = json_encode(array("Mensaje" => "La mesa fue dada de alta e insertada en la base de datos, se encuentra disponible para utilizar"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload  = json_encode(array("Error" => "Hubo un problema, NO se inserto la mesa en la base de datos"));
            $response = $response->withStatus(400);
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        
        return $response;
    }

    public function BajaMesa($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $id_bajaMesa = $data['id_mesa'];

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

        if(Mesa::EliminarMesa($id_bajaMesa))
        {
            Usuario::RegistrarOperacion($dataToken, "baja mesa");

            $payload = json_encode(array("Mensaje" => "La mesa a sido eliminada de la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "La mesa NO a sido eliminada de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ListarMesas($request, $response, $args)
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

        $lista = Mesa::Listar();

        Usuario::RegistrarOperacion($dataToken, "listado mesas");

        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }

    public function EntregarPedido($request, $response, $args)
    {
        $data = $request->getParsedBody();

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

        if(Mesa::CambiarEstado($data['entregar'], "cliente comiendo"))
        {
            Usuario::RegistrarOperacion($dataToken, "entrega pedido");

            $payload = json_encode(array("Mensaje" => "A la mesa id: " . $data['entregar'] . " Se le cambio el estado a 'cliente comiendo'"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload  = json_encode(array("Error" => "Hubo un problema, No se cambio el estado de la mesa, verifique parametros"));
            $response = $response->withStatus(400);
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        
        return $response;
    }

    public function CobrarPedido($request, $response, $args)
    {
        $data = $request->getParsedBody();

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

        if(Mesa::CambiarEstado($data['cobrar'], "cliente pagando"))
        {
            Usuario::RegistrarOperacion($dataToken, "cobro pedido");

            $payload = json_encode(array("Mensaje" => "A la mesa id: " . $data['cobrar'] . " Se le cambio el estado a 'cliente pagando'"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload  = json_encode(array("Error" => "Hubo un problema, No se cambio el estado de la mesa, verifique parametros"));
            $response = $response->withStatus(400);
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        
        return $response;
    }

    public function CerrarMesa($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = json_decode($data['cerrar']);

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

        if($dataToken->puesto == "socio")
        {
            if(Mesa::Cerrar($dataJson))
            {
                Usuario::RegistrarOperacion($dataToken, "cerrar mesa");

                $payload = json_encode(array("Mensaje" => "La mesa id: " . $dataJson->id . " fue cerrada. La encuesta de satisfaccion esta habilitada en /encuesta)"));
                $response = $response->withStatus(200);
            }
            else
            {
                $payload  = json_encode(array("Error" => "Hubo un problema, No se cerro la mesa, verifique parametros"));
                $response = $response->withStatus(400);
            }
        }
        else
        {
            $payload  = json_encode(array("Error" => "No esta habilitado para cerrar la mesa, solo socios habilitados"));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        
        return $response;
    }

    public function RealizarEncuesta($request, $response, $args)
    {
        $data = $request->getParsedBody();
      
     
        if(Pedido::EstaPago($data['id_pedido']))
        {
            
            if(Mesa::GuardarEncuesta($data))
            {
                $payload = json_encode(array("Mensaje" => "Gracias por responder nuestra encuesta de satisfaccion"));
                $response = $response->withStatus(200);
            }
            else
            {
                $payload = json_encode(array("Error" => "Hubo un problema al guardar la encuesta en la base de datos"));
                $response = $response->withStatus(500);
            }
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "No esta habilitado a responder la encuesta, intentelo cuando la mesa ".$data['id_mesa']." del pedido ".$data['id_pedido']." este cerrada"));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);
        return $response;

    }
}

?>