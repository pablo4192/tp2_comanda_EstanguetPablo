<?php
require_once "./db/accesoADatos.php";

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $clave;
    public $puesto;
    
    
    function __construct()
    {
        
    }
 
    
    public static function InsertarProducto($producto, $ingresado_por_id)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO productos (nombre,precio,stock,tipo,tiempo_preparacion,ingresado_por_id) VALUES (:nombre,:precio,:stock,:tipo,:tiempo_preparacion,:ingresado_por_id)");

        $consulta->bindValue(":nombre", $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $producto->precio, PDO::PARAM_INT); //No hay para float..
        $consulta->bindValue(":stock", $producto->stock, PDO::PARAM_INT);
        $consulta->bindValue(":tipo", $producto->tipo, PDO::PARAM_STR);
        $consulta->bindValue(":tiempo_preparacion", $producto->tiempo_preparacion, PDO::PARAM_INT);
        $consulta->bindValue(":ingresado_por_id", $ingresado_por_id, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            
            return true;
        }
        return false;
    }

    public static function Insertar($usuario)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO usuarios (nombre,apellido,clave,puesto) VALUES (:nombre,:apellido,:clave,:puesto)");

        $consulta->bindValue(":nombre", $usuario->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":apellido", $usuario->apellido, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $usuario->clave, PDO::PARAM_INT);
        $consulta->bindValue(":puesto", $usuario->puesto, PDO::PARAM_STR);

        $consulta->execute();
    
        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
           
            return true;
        }
        return false;
    }

    public static function InsertarDesdeCsv($usuario)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO usuarios (nombre,apellido,clave,puesto) VALUES (:nombre,:apellido,:clave,:puesto)");
        
     
        $consulta->bindValue(":nombre", $usuario->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":apellido", $usuario->apellido, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $usuario->clave, PDO::PARAM_INT);
        $consulta->bindValue(":puesto", $usuario->puesto, PDO::PARAM_STR);
      
        $consulta->execute();
        
        $filasAfectadas = $consulta->rowCount();
        
        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;  
    }

    public static function EliminarProducto($id_producto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("DELETE productos FROM productos WHERE id = :id_producto");

        $consulta->bindValue(":id_producto", $id_producto, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            
            return true;
        }
        return false;
    }

    public static function ModificarProducto($producto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        if(isset($producto->nombre))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE productos SET nombre = :nombre WHERE id = :id_producto");
            $consulta->bindValue(":nombre", $producto->nombre, PDO::PARAM_STR);
        }
        else if(isset($producto->precio))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE productos SET precio = :precio WHERE id = :id_producto");
            $consulta->bindValue(":precio", $producto->precio, PDO::PARAM_INT);
        }
        else if(isset($producto->stock))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE productos SET stock = :stock WHERE id = :id_producto");
            $consulta->bindValue(":stock", $producto->stock, PDO::PARAM_INT);
        }
        else if(isset($producto->tipo))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE productos SET tipo = :tipo WHERE id = :id_producto");
            $consulta->bindValue(":tipo", $producto->tipo, PDO::PARAM_STR);
        }
        else if(isset($producto->tiempo_preparacion))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE productos SET tiempo_preparacion = :tiempo_preparacion WHERE id = :id_producto");
            $consulta->bindValue(":tiempo_preparacion", $producto->tiempo_preparacion, PDO::PARAM_INT);
        }
        
        $consulta->bindValue(":id_producto", $producto->id, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            
            return true;
        }
        return false;
    }

    public static function ModificarUsuario($usuario)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        
        if(isset($usuario->nombre))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE usuarios SET nombre = :nombre WHERE id = :id_usuario");
            $consulta->bindValue(":nombre", $usuario->nombre, PDO::PARAM_STR);
        }
        else if(isset($usuario->apellido))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE usuarios SET apellido = :apellido WHERE id = :id_usuario");
            $consulta->bindValue(":apellido", $usuario->apellido, PDO::PARAM_STR);
        }
        else if(isset($usuario->clave))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE usuarios SET clave = :clave WHERE id = :id_usuario");
            $consulta->bindValue(":clave", $usuario->clave, PDO::PARAM_INT);
        }
        else if(isset($usuario->puesto))
        {
            $consulta = $accesoADatos->PrepararConsulta("UPDATE usuarios SET puesto = :puesto WHERE id = :id_usuario");
            $consulta->bindValue(":puesto", $usuario->puesto, PDO::PARAM_STR);
        }
       
        $consulta->bindValue(":id_usuario", $usuario->id, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            
            return true;
        }
        return false;
    }

    public static function EliminarUsuario($id_usuario)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("DELETE usuarios FROM usuarios WHERE id = :id_usuario");

        $consulta->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            
            return true;
        }
        return false;
    }

    public static function Listar()
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM usuarios");

        if($consulta->execute())
        {
            
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
        }
        return null;
    }
        
        

    public static function Existe($usuario)
    {
        $arrayDeUsuarios = self::Listar();

        foreach($arrayDeUsuarios as $u)
        {
            if($u->id == $usuario->id && $usuario->clave == $u->clave)
            {
                return true;
            }
        }
        return false;
    }

    public static function EsMozo($id_mozo) 
    {
        $arrayDeUsuarios = self::Listar();

        foreach($arrayDeUsuarios as $u)
        {
            if($u->id == $id_mozo && trim($u->puesto) == "mozo")
            {
                return true;
            }
            
        }
        return false;
    }

    public static function EsSocio($id_socio) 
    {
        $arrayDeUsuarios = self::Listar();

        foreach($arrayDeUsuarios as $u)
        {
            if($u->id == $id_socio && $u->puesto == "socio")
            {
                return true;
            }
        }
        return false;
    }

    public static function VerificarPuesto($id, $clave)
    {
        $arrayDeUsuarios = self::Listar();

        
        foreach($arrayDeUsuarios as $u)
        {
            if($u->clave == $clave && $u->id == $id)
            {
                return trim($u->puesto);
            }
        }
        return null;
    }

    public static function RetornarDatosUsuario($id)
    {
        $arrayUsuarios = self::Listar();

        foreach($arrayUsuarios as $u)
        {
            if($u->id == $id)
            {
                return array("nombre" => $u->nombre, "apellido" => $u->apellido, "clave" => $u->clave, "puesto" => $u->puesto);
            }
        }
        return null;
    }
    
    public static function RegistrarLogin($id_usuario)
    {
        $dataUsuario = self::RetornarDatosUsuario($id_usuario);
        $fecha = date("Y/m/d");
        $hora = date("H:i:s");

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO logins (id_usuario,nombre,apellido,fecha,hora) VALUES (:id_usuario,:nombre,:apellido,:fecha,:hora)");

        $consulta->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(":nombre", $dataUsuario['nombre'], PDO::PARAM_STR);
        $consulta->bindValue(":apellido", $dataUsuario['apellido'], PDO::PARAM_STR);
        $consulta->bindValue(":fecha", $fecha, PDO::PARAM_STR);
        $consulta->bindValue(":hora", $hora, PDO::PARAM_STR);

        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        if($filasAfectadas > 0)
        {
            return true;
        }
        return false;
    }

    public static function RegistrarOperacion($usuario, $tipo)
    {
        $fecha = date("Y/m/d");
        $hora = date("H:i:s");

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("INSERT INTO operaciones (id_usuario,puesto,tipo,fecha,hora) VALUES (:id_usuario,:puesto,:tipo,:fecha,:hora)");

        $consulta->bindValue(":id_usuario", $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(":puesto", $usuario->puesto, PDO::PARAM_STR);
        $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        $consulta->bindValue(":fecha", $fecha, PDO::PARAM_STR);
        $consulta->bindValue(":hora", $hora, PDO::PARAM_STR);

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