<?php

class AccesoADatos
{
    private static $objetoAccesoADatos;
    private $objetoPdo;

    private function __construct()
    {
        try
        {
            $this->objetoPdo = new PDO("mysql:host=localhost;dbname=tp2_comanda", "root");
        }
        catch(PDOException $ex)
        {
            echo "Ocurrio una excepcion al conectarse a la base de datos<br>Mensaje: " . $ex->getMessage();
            die();
        }
    }

    public static function RetornarAccesoADatos()
    {
        if(!isset(self::$objetoAccesoADatos))
        {
            self::$objetoAccesoADatos = new AccesoADatos();
        }
        return self::$objetoAccesoADatos;
    }

    public function PrepararConsulta($consultaSql)
    {
        return $this->objetoPdo->prepare($consultaSql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPdo->lastInsertId();
    }

}

?>