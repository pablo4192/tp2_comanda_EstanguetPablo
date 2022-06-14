<?php

class Imagen
{
    public $id_pedido;
    public $id_mesa;
    public $fecha;

    public function __construct($id_pedido , $id_mesa, $fecha)
    {
        $this->id_pedido = $id_pedido;
        $this->id_mesa = $id_mesa;
        $this->fecha = $fecha;

    }

    public function Subir($destino) 
    {
        if(!file_exists($destino))
        {
            mkdir("./".$destino);
        }

        if(!is_file($destino))
        {
            $nombreArchivo = "/"."idPedido".$this->id_pedido."_idMesa".$this->id_mesa."_".$this->fecha."_".$_FILES['imagen']['name'];
            $ruta = "./".$destino.$nombreArchivo;

            if(move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta))
            {
                return "Imagen subida con exito a ".$destino;
            }
            else
            {
                return "Hubo un problema al subir la imagen";
            }
        }
        else
        {
            return "El destino no es un directorio!";
        }
    }
}

?>