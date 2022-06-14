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
}



?>