<?php
require_once "./db/accesoADatos.php";

class Pedido
{
    public $id;    
   
    public $nombre_cliente;
    public $id_mozo;
    public $id_mesa;        //No se puede repetir si existe un pedido ya con el numero de mesa, validarlo!
    //Estos NO los pasa el mozo
    public $total;
    public $fecha;
    public $estado;

    public function __construct()
    {
        
    }

   
    public static function Insertar($pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO pedidos (nombre_cliente,id_mozo,id_mesa,total,fecha,estado) VALUES (:nombre_cliente,:id_mozo,:id_mesa,:total,:fecha,:estado)");

        
        $consulta->bindValue(":nombre_cliente", $pedido->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(":id_mozo", $pedido->id_mozo, PDO::PARAM_INT);
        $consulta->bindValue(":id_mesa", $pedido->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(":total", $pedido->total, PDO::PARAM_INT);
        $consulta->bindValue(":fecha", $pedido->fecha, PDO::PARAM_STR); //No me aparece en la base de datos la hora
        $consulta->bindValue(":estado", $pedido->estado, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $pedido->id = $accesoADatos->obtenerUltimoId();
            return true;
        }
    }

    public static function EliminarPedido($id_pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("DELETE pedidos FROM pedidos WHERE id = :id_pedido");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            if(self::EliminarRelaciones($id_pedido) && self::EliminarRelacionPedidoMesa($id_pedido))
            {
                return true;
            }
        }
        
        return false;
    }

    //Id_pedido, id_mozo, total, fecha No se pueden modificar ya que se generan en el programa; el id_mozo no deberia poder cambiarse ya que el mismo es el que toma el pedido
    public static function ModificarPedido($pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");

        if(isset($pedido->nombre_cliente) && !isset($pedido->id_mesa))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET nombre_cliente = :nombre_cliente WHERE id = :id_pedido");
            $consulta->bindValue(":nombre_cliente", $pedido->nombre_cliente, PDO::PARAM_STR);
        }
        else if(isset($pedido->id_mesa))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET id_mesa = :id_mesa WHERE id = :id_pedido");
            $consulta->bindValue(":id_mesa", $pedido->id_mesa, PDO::PARAM_INT);

            
            if(Mesa::EstaDisponible($pedido->id_mesa))
            {
                if(self::EliminarRelacionPedidoMesa($pedido->id))
                {
                    Mesa::OcuparMesa($pedido); 
                }
            }
            else
            {
                return false;
            }
        }
        else if(isset($pedido->estado)) //Solo el encargado del puesto preparacion
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id_pedido");
            $consulta->bindValue(":estado", $pedido->estado, PDO::PARAM_STR);
        }
        else if(isset($pedido->productos))
        {
            //Cambiar en tabla productos_pedidos y total en tabla pedidos
            self::EliminarRelaciones($pedido->id);
            
            if(self::Existe($pedido->id))
            {
                self::RelacionarProductosPedidos($pedido->productos, $pedido->id); 
            }

            $totalActualizado = self::CalcularTotal($pedido->productos);
            $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET total = :totalActualizado WHERE id = :id_pedido");
            $consulta->bindValue(":totalActualizado", $totalActualizado, PDO::PARAM_INT);
        }      
        
        $consulta->bindValue(":id_pedido", $pedido->id, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();
        
        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;
    }

    public static function CalcularTotal($productos)
    {
        $precios = array(); //Por si necesito el detalle de los precios, por ejemplo un ticket
        $total = 0;

        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("SELECT precio FROM productos WHERE id = :id");

        for($i = 0; $i < count($productos); $i++)
        {
            $cantidad = $productos[$i]->cantidad;
            while($cantidad > 0)
            {
                $consulta->bindValue(':id', $productos[$i]->id, PDO::PARAM_INT);
                $consulta->execute();

                $precioUnitario = $consulta->fetch(PDO::FETCH_ASSOC);
                array_push($precios, $precioUnitario);
                $total += $precioUnitario['precio'];
                $cantidad --;
            }
        }

        return $total;
    }

    public static function Listar()
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); //Cambiar por .env
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM pedidos");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");

    }

    public static function ListarProductosPendientesPreparacionXPuesto($puesto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); 
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM productos_pedidos WHERE puesto_preparacion = :puesto AND estado = 'pendiente'");

        $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function Relacionar($producto, $puesto_preparacion, $id_pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO productos_pedidos (id_pedido,id_producto,puesto_preparacion,estado) VALUES (:id_pedido,:id_producto,:puesto_preparacion,'pendiente')");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":id_producto", $producto->id, PDO::PARAM_INT);
        $consulta->bindValue(":puesto_preparacion", $puesto_preparacion, PDO::PARAM_STR);

        $consulta->execute();
    }

    private static function EliminarRelaciones($id_pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("DELETE productos_pedidos FROM productos_pedidos WHERE id_pedido = :id_pedido");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;

    }

    private static function EliminarRelacionPedidoMesa($id_pedido)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("UPDATE mesas SET nombre_cliente = '',id_pedido = '',estado = 'libre' WHERE id_pedido = :id_pedido");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;

    }
    

    public static function RelacionarProductosPedidos($productos, $id_pedido)
    {
        for($i = 0; $i < count($productos); $i++)
        {
            $cantidad = $productos[$i]->cantidad;

            while($cantidad > 0)
            {
                $puesto_preparacion = self::AsignarPuestoPreparacion($productos[$i]->id);

                
                self::Relacionar($productos[$i], $puesto_preparacion, $id_pedido);
                $cantidad --;
                
            }
        }
    }

    private static function AsignarPuestoPreparacion($id_producto)
    {
        $arrayProductos = Producto::Listar();

        foreach($arrayProductos as $p)
        {
            if($p->id == $id_producto)
            {
                return $p->tipo;
            }
        }
        return null;
    }

    private static function Existe($id)
    {
        $arrayPedidos = self::Listar();
        
        foreach($arrayPedidos as $p)
        {
            if($p->id == $id)
            {
                return true;
            }
        }
        return false;
    }

    public static function ListarProductosSegunEstado($id_pedido, $estado)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); 
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM productos_pedidos WHERE estado = :estado AND id_pedido = :id_pedido");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $estado, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function CambiarEstadoProducto_pedido($producto_pedido, $dataUsuario)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); 
        $consulta = $accesoADatos->PrepararConsulta("UPDATE productos_pedidos SET estado = :estado, id_encargado_preparacion = :id_encargado_preparacion WHERE id_pedido = :id_pedido AND puesto_preparacion = :puesto");

        $consulta->bindValue(":estado", $producto_pedido->estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $producto_pedido->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":id_encargado_preparacion", $dataUsuario->id, PDO::PARAM_INT);
        $consulta->bindValue(":puesto", $dataUsuario->puesto, PDO::PARAM_STR);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            $arrayEnPreparacion = self::ListarProductosSegunEstado($producto_pedido->id_pedido, "en preparacion");
            $arrayPendientes = self::ListarProductosSegunEstado($producto_pedido->id_pedido, "pendiente");
            
            if(count($arrayEnPreparacion) == 0 && count($arrayPendientes) == 0 && $producto_pedido->estado == "listo")
            {
                self::CambiarEstadoPedido($producto_pedido, "hora_egreso");
            }
            else if($producto_pedido->estado == "en preparacion")
            {
                $arrayPedidos = self::Listar();
                
                foreach($arrayPedidos as $p)
                {
                    if($p->id == $producto_pedido->id_pedido)
                    {
                        $horaIngreso = $p->hora_ingreso;
                        break;
                    }
                }
                
                if($horaIngreso == "")
                {
                    self::CambiarEstadoPedido($producto_pedido, "hora_ingreso");
                }
                else
                {
                    self::CambiarEstadoPedido($producto_pedido, "");
                }
            }
            return true;
        }
        return false;
       
    }

    public static function CambiarEstadoPedido($producto_pedido, $hora)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); 
        $horaActual = date('H:i:s');

        switch($hora)
        {
            case "hora_ingreso":
                var_dump($horaActual);
                $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET estado = :estado, hora_ingreso = :hora_ingreso WHERE id = :id_pedido");
                $consulta->bindValue(":hora_ingreso", $horaActual, PDO::PARAM_STR);
                break;
                case "hora_egreso":
                    var_dump($horaActual);
                    $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET estado = :estado, hora_egreso = :hora_egreso WHERE id = :id_pedido");
                    $consulta->bindValue(":hora_egreso", $horaActual, PDO::PARAM_STR);
                    break;
                    default:
                    var_dump($horaActual);
                    $consulta = $accesoADatos->PrepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id_pedido");
                    break;
        }
        

        $consulta->bindValue(":id_pedido", $producto_pedido->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $producto_pedido->estado, PDO::PARAM_STR);
        
        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;
       
    }

    public static function ListarProductosSegunIdPedidoYPuesto($id_pedido, $puesto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); 
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM productos_pedidos WHERE puesto_preparacion = :puesto AND id_pedido = :id_pedido");

        $consulta->bindValue(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function VerificarEstadoEnDB($producto_pedido, $puesto)
    {
        $arrayProductos = self::ListarProductosSegunIdPedidoYPuesto($producto_pedido->id_pedido, $puesto);
        $retorno = false;

        $estado = $arrayProductos[0]['estado'];
        

        if($estado == "pendiente" && $producto_pedido->estado == "en preparacion")
        {
            $retorno = true;
        }
        else if($estado == "en preparacion" && $producto_pedido->estado == "listo")
        {
            $retorno = true;
        }
        return $retorno;
    }

}

?>