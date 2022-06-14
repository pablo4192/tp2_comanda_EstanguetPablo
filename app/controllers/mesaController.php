<?php

class MesaController
{
    public function AltaMesa($request, $response, $args)
    {
        
        $data = $request->getParsedBody();
        $dataJson = $data['mesa'];
        $mesa = json_decode($dataJson);
        
        $mesaAInsertar = new Mesa();
        $mesaAInsertar->id = $mesa->id;
        $mesaAInsertar->id_pedido = ""; 
        $mesaAInsertar->nombre_cliente = "";
        $mesaAInsertar->estado = "libre";
        if(Mesa::Insertar($mesaAInsertar))
        {
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

        if(Mesa::EliminarMesa($id_bajaMesa))
        {
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
        $lista = Mesa::Listar();

        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }
}

?>