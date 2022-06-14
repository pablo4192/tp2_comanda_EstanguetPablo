<?php
require_once "./models/producto.php";
require_once "./models/usuario.php";


class ProductoController
{
    public function AltaProducto($request, $response, $args)
    {
        $data = $request->getParsedBody(); 
        $datajson = $data['producto'];
        $producto = json_decode($datajson);
       
        $header = $request->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $productoAInsertar = new Producto();
        $productoAInsertar->nombre = $producto->nombre;
        $productoAInsertar->precio = $producto->precio;
        $productoAInsertar->stock = $producto->stock;
        $productoAInsertar->tipo = $producto->tipo;
        $productoAInsertar->tiempo_preparacion = $producto->tiempo_preparacion;
        
        $dataToken = Jwtoken::Verificar($token);
        
        if(Usuario::InsertarProducto($productoAInsertar, $token->id))
        {
            $payload = json_encode(array("mensaje:" => "Producto insertado en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("mensaje:" => "Hubo un problema al insertar el producto a la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;

    }

    public function BajaProducto($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $id_bajaProducto = $data['id_producto'];

        if(Usuario::EliminarProducto($id_bajaProducto))
        {
            $payload = json_encode(array("Mensaje" => "El producto a sido eliminado de la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El producto NO a sido eliminado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ModificarProducto($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        if(array_key_exists("nombre", $data))
        {
            $ProductoAModificar = json_decode($data['nombre']);
        }
        else if(array_key_exists("precio", $data))
        {
            $ProductoAModificar = json_decode($data['precio']);
        }
        else if(array_key_exists("stock", $data))
        {
            $ProductoAModificar = json_decode($data['stock']);
        }
        else if(array_key_exists("tipo", $data))
        {
            $ProductoAModificar = json_decode($data['tipo']);
        }
        else if(array_key_exists("tiempo_preparacion", $data))
        {
            $ProductoAModificar = json_decode($data['tiempo_preparacion']);
        }

        if(Usuario::ModificarProducto($ProductoAModificar))
        {
            $payload = json_encode(array("Mensaje" => "El producto a sido modificado en la base de datos"));
            $response = $response->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("Error" => "El producto NO a sido modificado de la base de datos"));
            $response = $response->withStatus(400); 
        }
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function ListarProductos($request, $response, $args)
    {
        $lista = Producto::Listar();

        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        return $response;
    }
}

?>