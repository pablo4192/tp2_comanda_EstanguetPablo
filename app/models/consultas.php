<?php
require_once "./db/accesoADatos.php";

class Consultas
{
    public function __construct()
    {

    }

    public function ConsultarLogins()
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM logins");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ConsultarLoginsParam($desde, $hasta)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM logins WHERE fecha >= :desde AND fecha <= :hasta");

        $consulta->bindValue(":desde", $desde,PDO::PARAM_STR);
        $consulta->bindValue(":hasta", $hasta,PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ConsultarOperacionesPorSector($puesto)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE puesto = :puesto");

        $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ConsultarOperacionesPorSectorParam($puesto, $desde, $hasta)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE puesto = :puesto AND fecha >= :desde AND fecha <= :hasta");

        $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);
        $consulta->bindValue(":desde", $desde,PDO::PARAM_STR);
        $consulta->bindValue(":hasta", $hasta,PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ConsultarOperacionesPorSector_PorEmpleado($puesto)
    {
        $usuarios = Usuario::Listar();
        $arrayOperacionesPorEmpleado = [];
        $id_usuario;
        
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($usuarios as $u)
        {
            
            if($u->puesto == $puesto)
            {
                $id_usuario = $u->id;
                
                $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE puesto = :puesto AND id_usuario = :id_usuario");
                $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);
                $consulta->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
                
                $consulta->execute();

                $operacionesUsuario = array("Operaciones usuario id: ".$id_usuario => $consulta->fetchAll(PDO::FETCH_ASSOC));

                array_push($arrayOperacionesPorEmpleado, $operacionesUsuario);
              
            }
        }

        return $arrayOperacionesPorEmpleado;
    }
}

?>