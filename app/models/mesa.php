<?php
require_once "./db/AccesoADatos.php";

class Mesa
{
    public $id;
    public $nombre_cliente;
    public $id_pedido;
    public $estado;

    public function __construct()
    {
       
    }

    public static function Insertar($mesa)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO mesas (id,nombre_cliente,id_pedido,estado) VALUES (:id,:nombre_cliente,:id_pedido,:estado)");

        $consulta->bindValue(":id", $mesa->id, PDO::PARAM_STR);
        $consulta->bindValue(":nombre_cliente", $mesa->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $mesa->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $mesa->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function EliminarMesa($id_mesa)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("DELETE mesas FROM mesas WHERE id = :id_mesa");

        $consulta->bindValue(":id_mesa", $id_mesa, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;
    }

    public static function OcuparMesa($pedido) 
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("UPDATE mesas SET estado = 'cliente esperando el pedido', id_pedido = :id_pedido, nombre_cliente = :nombre_cliente WHERE id = :id_mesa");

        $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $pedido->id, PDO::PARAM_STR);
        $consulta->bindValue(':nombre_cliente', $pedido->nombre_cliente, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function Listar()
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM mesas");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");

    }

    public static function EstaDisponible($id_mesa)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT id,estado FROM mesas WHERE id = :id_mesa");

        $consulta->bindValue(":id_mesa", $id_mesa, PDO::PARAM_STR);
        $consulta->execute();

        $retorno = $consulta->fetch(PDO::FETCH_NUM);

        if($retorno)
        {
            if($retorno[0] == $id_mesa && $retorno[1] == "cerrada")
            {
                return true;
            }
        }
        return false;
    }

    public static function CambiarEstado($id_mesa, $estado)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id_mesa");

        $consulta->bindValue(":estado", $estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_mesa", $id_mesa, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;

    }

    public static function Cerrar($data)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("UPDATE mesas SET estado = 'cerrada',nombre_cliente = '',id_pedido = '' WHERE id = :id_mesa"); 

        $consulta->bindValue(":id_mesa", $data->id, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            if(Pedido::CerrarPedido($data))
            {
                return true;
            }
        }
        return false;
    }

    public static function ExisteId($id)
    {
        $listaMesas = self::Listar();

        foreach($listaMesas as $m)
        {
            if($m->id == $id)
            {
                return true;
            }
        }
        return false;
    }

    public static function GuardarEncuesta($data)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO encuestas (id_pedido,id_mesa,mesa,restaurant,mozo,cocinero,comentarios) VALUES (:id_pedido,:id_mesa,:mesa,:restaurant,:mozo,:cocinero,:comentarios)");

        $consulta->bindValue(":id_pedido", $data['id_pedido'], PDO::PARAM_STR);
        $consulta->bindValue(":id_mesa", $data['id_mesa'], PDO::PARAM_STR);
        $consulta->bindValue(":mesa", $data['mesa'], PDO::PARAM_INT);
        $consulta->bindValue(":restaurant", $data['restaurant'], PDO::PARAM_INT);
        $consulta->bindValue(":mozo", $data['mozo'], PDO::PARAM_INT);
        $consulta->bindValue(":cocinero", $data['cocinero'], PDO::PARAM_INT);
        $consulta->bindValue(":comentarios", $data['comentarios'], PDO::PARAM_STR);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;


    }
        
}


?>