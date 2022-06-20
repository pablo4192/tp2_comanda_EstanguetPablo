<?php
require_once "./db/accesoADatos.php";

class Producto
{
    public $id; //Lo obtengo de la db
    public $nombre;
    public $precio;
    public $stock;
    public $tipo;
    public $tiempo_preparacion;
    public $ingresado_por_id;
   

    public function __construct() //Constructor vacio para fetch
    {
        
    }

    public static function Listar()
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); //Cambiar por .env
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM productos");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");

    }

    public static function Existe($producto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda"); //Cambiar por .env
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM productos WHERE id = :id");

        $consulta->bindValue(':id', $producto->id, PDO::PARAM_STR);

        $consulta->execute();

        if(!$consulta->fetch(PDO::FETCH_ASSOC))
        {
            return false;
        }
        return true;
    }

    public static function InsertarDesdeCsv($producto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos("tp2_comanda");
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO productos (nombre,precio,stock,tipo,tiempo_preparacion,ingresado_por_id) VALUES (:nombre,:precio,:stock,:tipo,:tiempo_preparacion,:ingresado_por_id)");
        
        
        $consulta->bindValue(":nombre", $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $producto->precio, PDO::PARAM_INT);
        $consulta->bindValue(":stock", $producto->stock, PDO::PARAM_INT);
        $consulta->bindValue(":tipo", $producto->tipo, PDO::PARAM_STR);
        $consulta->bindValue(":tiempo_preparacion", $producto->tiempo_preparacion, PDO::PARAM_INT); 
        $consulta->bindValue(":ingresado_por_id", $producto->ingresado_por_id, PDO::PARAM_INT);
        
        try
        {
            $consulta->execute();
        }
        catch(Exception $e)
        {
            return false;
        }

        
        $filasAfectadas = $consulta->rowCount();
        
        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;  
    }
}



?>