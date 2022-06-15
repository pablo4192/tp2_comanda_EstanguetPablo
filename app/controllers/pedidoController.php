<?php
require_once "./models/pedido.php";
require_once "./models/mesa.php";
require_once "./models/imagen.php";

class PedidoController
{
    public function AltaPedido($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $dataJson = $data['pedido'];
        $pedido = json_decode($dataJson);

        
        $pedidoAInsertar = new Pedido();
        
        $pedidoAInsertar->nombre_cliente = $pedido->nombre_cliente;
        $pedidoAInsertar->id_mozo = $pedido->id_mozo;
        $pedidoAInsertar->id_mesa = $pedido->id_mesa;
        $pedidoAInsertar->total = Pedido::CalcularTotal($pedido->productos);
        $pedidoAInsertar->fecha = date("y-m-d");  
        $pedidoAInsertar->estado = "pendiente";
        
        if(Pedido::Insertar($pedidoAInsertar))
        {
            //Subida Imagen Opcional
            if(array_key_exists("imagen", $_FILES))
            {
                $imagen = new Imagen($pedidoAInsertar->id, $pedidoAInsertar->id_mesa, $pedidoAInsertar->fecha);
                $mensajeImagen = $imagen->Subir("imagenes_Pedidos");
                $response->getBody()->write($mensajeImagen."/ ");
            }

            Mesa::OcuparMesa($pedidoAInsertar); 

            Pedido::RelacionarProductosPedidos($pedido->productos,$pedidoAInsertar->id);
            
            $payload = json_encode(array("mensaje" => "El pedido fue ingresado con exito, la mesa se marco como ocupada y se le asocio el pedido"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Hubo un problema, NO se ingreso el pedido a la base de datos"));
            $response = $response->withStatus(400);
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
    
        return $response;
    }

    public function BajaPedido($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $id_bajaPedido = $data['id_pedido'];

        if(Pedido::EliminarPedido($id_bajaPedido))
        {
            $payload = json_encode(array("Mensaje" => "El pedido a sido eliminado de la base de datos, la relacion con la mesa y la relacion productos_pedido tambien fueron eliminadas"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El pedido NO a sido eliminado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ModificarPedido($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        if(array_key_exists("nombre_cliente", $data))
        {
            $pedidoAModificar = json_decode($data['nombre_cliente']);
        }
        else if(array_key_exists("id_mesa", $data))
        {
            $pedidoAModificar = json_decode($data['id_mesa']);
        }
        else if(array_key_exists("estado", $data)) //Para que cambien los encargados de preparacion
        {
            $pedidoAModificar = json_decode($data['estado']);
        }
        else if(array_key_exists("productos", $data))
        {
            //Modificar relacion productos_pedidos //Modificar total del pedido
            $pedidoAModificar = json_decode($data['productos']);
        }

        if(Pedido::ModificarPedido($pedidoAModificar))
        {
            $payload = json_encode(array("Mensaje" => "El pedido a sido modificado en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El pedido NO a sido modificado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ListarPedidos($request, $response, $args)
    {
        $lista = Pedido::Listar();

        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }

    public function ListarProductos_PedidosPendientes($request, $response, $args)
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

        $listaPendientes = Pedido::ListarProductosPendientesPreparacion($dataToken->puesto);

        $payload = json_encode(array("listaProductos_PendientesPreparacion" => $listaPendientes));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
        


    }

    public function CambiarEstadoProducto_pedido($request, $response, $args)
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

        if(array_key_exists("en_preparacion", $data))
        {
            $producto_pedido = json_decode($data['en_preparacion']);
        }
        else if(array_key_exists("listo", $data))
        {
            $producto_pedido = json_decode($data['listo']);
        }

        if(Pedido::CambiarEstadoProducto_pedido($producto_pedido, $dataToken))
        {
            $payload = json_encode(array("Mensaje" => "El producto relacionado con el pedido " . $producto_pedido->id_pedido . " a sido modificado en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El estado del producto relacionado con el pedido " . $producto_pedido->id_pedido  . " NO a sido modificado en la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}

?>