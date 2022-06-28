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

    public function ConsultarOperacionesPorSector_PorEmpleadoParam($puesto, $desde, $hasta)
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
                
                $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE puesto = :puesto AND id_usuario = :id_usuario AND fecha >= :desde AND fecha <= :hasta");
                $consulta->bindValue(":puesto", $puesto, PDO::PARAM_STR);
                $consulta->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
                $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
                $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);


                $consulta->execute();

                $retorno = $consulta->fetchAll(PDO::FETCH_ASSOC);

                if(count($retorno) == 0)
                {
                    $operacionesUsuario = array("Operaciones usuario id: ".$id_usuario => "No posee operaciones");
                }
                else
                {
                    $operacionesUsuario = array("Operaciones usuario id: ".$id_usuario => $retorno);
                }

                array_push($arrayOperacionesPorEmpleado, $operacionesUsuario);
              
            }
        }

        return $arrayOperacionesPorEmpleado;
    }

    public function ConsultarOperaciones_PorIdEmpleado($id)
    {
        $usuarios = Usuario::Listar();
        $operacionesUsuario = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($usuarios as $u)
        {
            if($u->id == $id)
            {            
                $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE id_usuario = :id");
                $consulta->bindValue(":id", $id, PDO::PARAM_INT);
                
                $consulta->execute();

                $operacionesUsuario = array("Operaciones usuario id: ".$id => $consulta->fetchAll(PDO::FETCH_ASSOC));
            }
        }

        return $operacionesUsuario;
    }

    public function ConsultarOperaciones_PorIdEmpleadoParam($id, $desde, $hasta)
    {
        $usuarios = Usuario::Listar();
        $operacionesUsuario = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($usuarios as $u)
        {
            if($u->id == $id)
            {            
                $consulta = $accesoADatos->PrepararConsulta("SELECT * FROM operaciones WHERE id_usuario = :id AND fecha >= :desde AND fecha <= :hasta");
                $consulta->bindValue(":id", $id, PDO::PARAM_INT);
                $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
                $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);

                $consulta->execute();

                $operacionesUsuario = array("Operaciones usuario id: ".$id => $consulta->fetchAll(PDO::FETCH_ASSOC));
            }
        }

        return $operacionesUsuario;
    }

    public function ConsultarMasVendido()
    {
        $productos = Producto::Listar();
       
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMasVendido = [];
        $contador = 0;
        $contadorMasVendido = 1;
        $productosMasVendidos = []; 

        foreach($productos as $p)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM productos_pedidos WHERE id_producto = :id_producto");
            $consulta->bindValue(":id_producto", $p->id, PDO::PARAM_INT);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];
            
            if($contador >= $contadorMasVendido)
            {
                if($contador > $contadorMasVendido)
                {
                    array_splice($idMasVendido, 0);
                }

                $contadorMasVendido = $contador;
                array_push($idMasVendido, $p->id);
            }

        }

        foreach($idMasVendido as $idProd)
        {
            foreach($productos as $p)
            {
                if($p->id == $idProd)
                {
                    array_push($productosMasVendidos, $p);
                }
            }
        }

        return $productosMasVendidos;

    }

    public function ConsultarMasVendido_Param($desde, $hasta)
    {
        $productos = Producto::Listar();
       
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMasVendido = [];
        $contador = 0;
        $contadorMasVendido = 1;
        $productosMasVendidos = []; 

        foreach($productos as $p)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM productos_pedidos WHERE id_producto = :id_producto AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_producto", $p->id, PDO::PARAM_INT);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];
            
            if($contador >= $contadorMasVendido)
            {
                if($contador > $contadorMasVendido)
                {
                    array_splice($idMasVendido, 0);
                }

                $contadorMasVendido = $contador;
                array_push($idMasVendido, $p->id);
            }

        }

        foreach($idMasVendido as $idProd)
        {
            foreach($productos as $p)
            {
                if($p->id == $idProd)
                {
                    array_push($productosMasVendidos, $p);
                }
            }
        }

        return $productosMasVendidos;
    }

    public function ConsultarMenosVendido()
    {
        $productos = Producto::Listar();
        
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMenosVendido = [];
        $contador = 0;
        $flag = true;
        $contadorMenosVendido;
        $productosMenosVendidos = []; 

        foreach($productos as $p)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM productos_pedidos WHERE id_producto = :id_producto");
            $consulta->bindValue(":id_producto", $p->id, PDO::PARAM_INT);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($contador != 0)
            {
                if($flag || $contador <= $contadorMenosVendido)
                {
                    if($flag)
                    {
                        $contadorMenosVendido = $contador;
                    }
    
                    if($contador < $contadorMenosVendido)
                    {
                        array_splice($idMenosVendido, 0);
                    }
                    
                    if(!$flag)
                    {
                        $contadorMenosVendido = $contador;
                    }
    
                    array_push($idMenosVendido, $p->id);
                    
                    $flag = false;
                }
            }
        }

        foreach($idMenosVendido as $idProd)
        {
            foreach($productos as $p)
            {
                if($p->id == $idProd)
                {
                    array_push($productosMenosVendidos, $p);
                }
            }
        }

        return $productosMenosVendidos;
    }

    public function ConsultarMenosVendido_Param($desde, $hasta)
    {
        $productos = Producto::Listar();

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMenosVendido = [];
        $contador = 0;
        $flag = true;
        $contadorMenosVendido;
        $productosMenosVendidos = []; 

        foreach($productos as $p)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM productos_pedidos WHERE id_producto = :id_producto AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_producto", $p->id, PDO::PARAM_INT);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($contador != 0)
            {
                if($flag || $contador <= $contadorMenosVendido)
                {
                    if($flag)
                    {
                        $contadorMenosVendido = $contador;
                    }
    
                    if($contador < $contadorMenosVendido)
                    {
                        array_splice($idMenosVendido, 0);
                    }
                    
                    if(!$flag)
                    {
                        $contadorMenosVendido = $contador;
                    }
    
                    array_push($idMenosVendido, $p->id);
                    
                    $flag = false;
                }
            }
        }

        foreach($idMenosVendido as $idProd)
        {
            foreach($productos as $p)
            {
                if($p->id == $idProd)
                {
                    array_push($productosMenosVendidos, $p);
                }
            }
        }

        return $productosMenosVendidos;
    }

    public function ConsultarPedidosFueraDeTiempo()
    {
        $pedidos = Pedido::Listar();
        $pedidosFueraDeTiempo = [];

        foreach($pedidos as $p)
        {
            if($p->hora_ingreso != "" && $p->hora_egreso != "")
            {
                $tiempoEstimado = $p->tiempo_estimado;
                $horaIngreso = explode(":", $p->hora_ingreso)[0];
                $horaEgreso = explode(":", $p->hora_egreso)[0];
                $minutoIngreso = explode(":", $p->hora_ingreso)[1];
                $minutoEgreso = explode(":", $p->hora_egreso)[1];
    
                if($horaEgreso == $horaIngreso)
                {
                    $minutosPasados = $minutoEgreso - $minutoIngreso;
                    
                    if($minutosPasados > $tiempoEstimado)
                    {
                        array_push($pedidosFueraDeTiempo, $p);
                    }
                }
                else if($horaEgreso != $horaIngreso)
                {
                    if($horaEgreso > $horaIngreso + 1)
                    {
                        array_push($pedidosFueraDeTiempo, $p);
                    }
                    else
                    {
                        $minutosAux = $minutoIngreso - $minutoEgreso;
                        $minutosPasados = 60 - $minutosAux;
    
                        if($minutosPasados > $tiempoEstimado)
                        {
                            array_push($pedidosFueraDeTiempo, $p);
                        }
                    }
                }

            }
            

        }
        return $pedidosFueraDeTiempo;
    }
     
    public function ConsultarPedidosFueraDeTiempo_Param($desde, $hasta)
    {
        $pedidos = Pedido::ListarPedidosEntreFechas($desde, $hasta);
        $pedidosFueraDeTiempo = [];

        foreach($pedidos as $p)
        {
            if($p->hora_ingreso != "" && $p->hora_egreso != "")
            {
                $tiempoEstimado = $p->tiempo_estimado;
                $horaIngreso = explode(":", $p->hora_ingreso)[0];
                $horaEgreso = explode(":", $p->hora_egreso)[0];
                $minutoIngreso = explode(":", $p->hora_ingreso)[1];
                $minutoEgreso = explode(":", $p->hora_egreso)[1];
    
                if($horaEgreso == $horaIngreso)
                {
                    $minutosPasados = $minutoEgreso - $minutoIngreso;
                    
                    if($minutosPasados > $tiempoEstimado)
                    {
                        array_push($pedidosFueraDeTiempo, $p);
                    }
                }
                else if($horaEgreso != $horaIngreso)
                {
                    if($horaEgreso > $horaIngreso + 1)
                    {
                        array_push($pedidosFueraDeTiempo, $p);
                    }
                    else
                    {
                        $minutosAux = $minutoIngreso - $minutoEgreso;
                        $minutosPasados = 60 - $minutosAux;
    
                        if($minutosPasados > $tiempoEstimado)
                        {
                            array_push($pedidosFueraDeTiempo, $p);
                        }
                    }
                }

            }
            

        }
        return $pedidosFueraDeTiempo;
    }

    public function ConsultarPedidosCancelados()
    {
        $pedidos = Pedido::Listar();
        $arrayCancelados = [];

        foreach($pedidos as $p)
        {
            if($p->estado == "cancelado")
            {
                array_push($arrayCancelados, $p);
            }
        }
        return $arrayCancelados;
    }

    public function ConsultarPedidosCancelados_Param($desde, $hasta)
    {
        $pedidos = Pedido::ListarPedidosEntreFechas($desde, $hasta);
        $arrayCancelados = [];

        foreach($pedidos as $p)
        {
            if($p->estado == "cancelado")
            {
                array_push($arrayCancelados, $p);
            }
        }
        return $arrayCancelados;
    }

    public function ConsultarMesaMasUsada()
    {
        $mesas = Mesa::Listar();

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMasUsada = [];
        $contador = 0;
        $contadorMasUsada = 1;
        $mesasMasUsadas = []; 

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM pedidos WHERE id_mesa = :id_mesa");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];
            
            if($contador >= $contadorMasUsada)
            {
                if($contador > $contadorMasUsada)
                {
                    array_splice($idMasUsada, 0);
                }

                $contadorMasUsada = $contador;
                array_push($idMasUsada, $m->id);
            }

        }

        foreach($idMasUsada as $idMesa)
        {
            foreach($mesas as $m)
            {
                if($m->id == $idMesa)
                {
                    array_push($mesasMasUsadas, $m);
                }
            }
        }

        return $mesasMasUsadas;

    }

    public function ConsultarMesaMasUsada_Param($desde, $hasta)
    {
        $mesas = Mesa::Listar();

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMasUsada = [];
        $contador = 0;
        $contadorMasUsada = 1;
        $mesasMasUsadas = []; 

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM pedidos WHERE id_mesa = :id_mesa AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];
            
            if($contador >= $contadorMasUsada)
            {
                if($contador > $contadorMasUsada)
                {
                    array_splice($idMasUsada, 0);
                }

                $contadorMasUsada = $contador;
                array_push($idMasUsada, $m->id);
            }

        }

        foreach($idMasUsada as $idMesa)
        {
            foreach($mesas as $m)
            {
                if($m->id == $idMesa)
                {
                    array_push($mesasMasUsadas, $m);
                }
            }
        }

        return $mesasMasUsadas;
    }

    public function ConsultarMesaMenosUsada()
    {
        $mesas = Mesa::Listar();
        
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMenosUsada = [];
        $contador = 0;
        $flag = true;
        $contadorMenosUsada;
        $mesasMenosUsadas = []; 

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM pedidos WHERE id_mesa = :id_mesa");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($contador != 0)
            {
                if($flag || $contador <= $contadorMenosUsada)
                {
                    if($flag)
                    {
                        $contadorMenosUsada = $contador;
                    }
    
                    if($contador < $contadorMenosUsada)
                    {
                        array_splice($idMenosUsada, 0);
                    }
                    
                    if(!$flag)
                    {
                        $contadorMenosUsada = $contador;
                    }
    
                    array_push($idMenosUsada, $m->id);
                    
                    $flag = false;
                }
            }
        }

        foreach($idMenosUsada as $idMesa)
        {
            foreach($mesas as $m)
            {
                if($m->id == $idMesa)
                {
                    array_push($mesasMenosUsadas, $m);
                }
            }
        }

        return $mesasMenosUsadas;
    }

    public function ConsultarMesaMenosUsada_Param($desde, $hasta)
    {
        $mesas = Mesa::Listar();
        
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $idMenosUsada = [];
        $contador = 0;
        $flag = true;
        $contadorMenosUsada;
        $mesasMenosUsadas = []; 

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT count(*) FROM pedidos WHERE id_mesa = :id_mesa AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->execute();

            $contador = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($contador != 0)
            {
                if($flag || $contador <= $contadorMenosUsada)
                {
                    if($flag)
                    {
                        $contadorMenosUsada = $contador;
                    }
    
                    if($contador < $contadorMenosUsada)
                    {
                        array_splice($idMenosUsada, 0);
                    }
                    
                    if(!$flag)
                    {
                        $contadorMenosUsada = $contador;
                    }
    
                    array_push($idMenosUsada, $m->id);
                    
                    $flag = false;
                }
            }
        }

        foreach($idMenosUsada as $idMesa)
        {
            foreach($mesas as $m)
            {
                if($m->id == $idMesa)
                {
                    array_push($mesasMenosUsadas, $m);
                }
            }
        }

        return $mesasMenosUsadas;
    }

    public function ConsultarMesaMasFacturacion()
    {
        $mesas = Mesa::Listar();
        $total = 0;
        $mayorFacturacion = 0;
        
        $mesasMasFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT SUM(total) FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado'");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
           
            $consulta->execute();

            $total = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($total != null)
            {
                if($total >= $mayorFacturacion)
                {
                    
                    if($total > $mayorFacturacion)
                    {
                        array_splice($mesasMasFacturacion, 0);
                    }
    
                    $mayorFacturacion = $total;
                    array_push($mesasMasFacturacion, $m);
                }
            }

        }

        
        return $mesasMasFacturacion;
    }

    public function ConsultarMesaMasFacturacion_Param($desde, $hasta)
    {
        $mesas = Mesa::Listar();
        $total = 0;
        $mayorFacturacion = 0;
        
        $mesasMasFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT SUM(total) FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado' AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
           
            $consulta->execute();
                     
            $total = $consulta->fetch(PDO::FETCH_NUM)[0];
            
            if($total != null)
            {
                if($total >= $mayorFacturacion)
                {
                   
                    if($total > $mayorFacturacion)
                    {
                        array_splice($mesasMasFacturacion, 0);
                    }
    
                    $mayorFacturacion = $total;
                    array_push($mesasMasFacturacion, $m);
                }
            }

        }

        
        return $mesasMasFacturacion;
    }

    public function ConsultarMesaMenosFacturacion()
    {
        $mesas = Mesa::Listar();
        $total = 0;
        $flag = true;
        $mayorFacturacion;
        
        $mesasMenosFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT SUM(total) FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado'");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
           
            $consulta->execute();

            $total = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($total != null)
            {
                if($flag || $total <= $menorFacturacion)
                {
                    if($flag)
                    {
                        $menorFacturacion = $total;
                    }
                    
                    if($total < $menorFacturacion)
                    {
                        array_splice($mesasMenosFacturacion, 0);
                    }

                    if(!$flag)
                    {
                        $menorFacturacion = $total;
                    }
    
                    array_push($mesasMenosFacturacion, $m);
                    $flag = false;
                }
            }
        }
        return $mesasMenosFacturacion;
    }

    public function ConsultarMesaMenosFacturacion_Param($desde, $hasta)
    {
        $mesas = Mesa::Listar();
        $total = 0;
        $flag = true;
        $mayorFacturacion;
        
        $mesasMenosFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT SUM(total) FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado' AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
           
            $consulta->execute();

            $total = $consulta->fetch(PDO::FETCH_NUM)[0];

            if($total != null)
            {
                if($flag || $total <= $menorFacturacion)
                {
                    if($flag)
                    {
                        $menorFacturacion = $total;
                    }
                    
                    if($total < $menorFacturacion)
                    {
                        array_splice($mesasMenosFacturacion, 0);
                    }

                    if(!$flag)
                    {
                        $menorFacturacion = $total;
                    }
    
                    array_push($mesasMenosFacturacion, $m);
                    $flag = false;
                }
            }
        }
        return $mesasMenosFacturacion;
    }

    public function ConsultarMesaMayorImporte()
    {
        $mesas = Mesa::Listar();
        $dataConsulta = [];
        $mayorFacturacion = 0;
        
        $mesasMasFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT id_mesa,total FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado'");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
           
            $consulta->execute();

            $dataConsulta = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if($dataConsulta != null)
            {
                foreach($dataConsulta as $d)
                {
                    if($d['total'] >= $mayorFacturacion)
                    {
                        if($d['total'] > $mayorFacturacion)
                        {
                            array_splice($mesasMasFacturacion, 0);
                        }
            
                        $mayorFacturacion = $d['total'];
                        array_push($mesasMasFacturacion, $d);
                    }
    
                }
            }
        }

        return $mesasMasFacturacion;
    }

    public function ConsultarMesaMayorImporte_Param($desde, $hasta)
    {
        $mesas = Mesa::Listar();
        $dataConsulta = [];
        $mayorFacturacion = 0;
        
        $mesasMasFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT id_mesa,total FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado' AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
           
            $consulta->execute();

            $dataConsulta = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if($dataConsulta != null)
            {
                foreach($dataConsulta as $d)
                {
                    if($d['total'] >= $mayorFacturacion)
                    {
                        if($d['total'] > $mayorFacturacion)
                        {
                            array_splice($mesasMasFacturacion, 0);
                        }
            
                        $mayorFacturacion = $d['total'];
                        array_push($mesasMasFacturacion, $d);
                    }
    
                }
            }
        }

        return $mesasMasFacturacion;
    }

    public function ConsultarMesaMenorImporte()
    {
        $mesas = Mesa::Listar();
        $dataConsulta = [];
        $flag = true;
        
        
        $mesasMenosFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT id_mesa,total FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado'");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
           
            $consulta->execute();

            $dataConsulta = $consulta->fetchAll(PDO::FETCH_ASSOC);

            
           
            if($dataConsulta != null)
            {
                foreach($dataConsulta as $d)
                {
                    if($flag || $d['total'] <= $menorFacturacion)
                    {                
                        if($flag)
                        {
                            $menorFacturacion = $d['total'];
                        }
                        
                        if($d['total'] < $menorFacturacion)
                        {
                            array_splice($mesasMenosFacturacion, 0);
                        }
    
                        if(!$flag)
                        {
                            $menorFacturacion = $d['total'];
                        }
        
                        array_push($mesasMenosFacturacion, $d);
                        $flag = false;

                    }
                }
            }
        }
        return $mesasMenosFacturacion;
    }

    public function ConsultarMesaMenorImporte_Params($desde, $hasta)
    {
        $mesas = Mesa::Listar();
        $dataConsulta = [];
        $flag = true;
        
        
        $mesasMenosFacturacion = [];

        $accesoADatos = AccesoADatos::RetornarAccesoADatos();

        foreach($mesas as $m)
        {
            $consulta = $accesoADatos->PrepararConsulta("SELECT id_mesa,total FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado' AND fecha >= :desde AND fecha <= :hasta");
            $consulta->bindValue(":id_mesa", $m->id, PDO::PARAM_STR);
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
           
            $consulta->execute();

            $dataConsulta = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if($dataConsulta != null)
            {
                foreach($dataConsulta as $d)
                {
                    if($flag || $d['total'] <= $menorFacturacion)
                    {                
                        if($flag)
                        {
                            $menorFacturacion = $d['total'];
                        }
                        
                        if($d['total'] < $menorFacturacion)
                        {
                            array_splice($mesasMenosFacturacion, 0);
                        }
    
                        if(!$flag)
                        {
                            $menorFacturacion = $d['total'];
                        }
        
                        array_push($mesasMenosFacturacion, $d);
                        $flag = false;

                    }
                }
            }
        }
        return $mesasMenosFacturacion;
    }
        
    public function ConsultarFacturacionMesa($id, $desde, $hasta)
    {
        $accesoADatos = AccesoADatos::RetornarAccesoADatos();
        $consulta = $accesoADatos->PrepararConsulta("SELECT SUM(total) FROM pedidos WHERE id_mesa = :id_mesa AND estado != 'cancelado' AND fecha >= :desde AND fecha <= :hasta");

        $consulta->bindValue(":id_mesa", $id, PDO::PARAM_STR);
        $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_NUM);


    }

    public function ConsultarComentarios($opcion)
    {
        $encuestas = Mesa::ListarEncuestas();
        $acumulador = 0;
        $arrayMejoresComentarios = [];
        $arrayPeoresComentarios = [];

        switch($opcion)
        {
            case "mejores":
                foreach($encuestas as $e)
                {
                    $acumulador += $e['mesa'];
                    $acumulador += $e['restaurant'];
                    $acumulador += $e['mozo'];
                    $acumulador += $e['cocinero'];

                    if($acumulador / 4 > 6)
                    {
                        array_push($arrayMejoresComentarios, $e);
                    }
                    $acumulador = 0;
                }
                return $arrayMejoresComentarios;
                break;
                case "peores":
                    foreach($encuestas as $e)
                    {
                        $acumulador += $e['mesa'];
                        $acumulador += $e['restaurant'];
                        $acumulador += $e['mozo'];
                        $acumulador += $e['cocinero'];

                        if($acumulador / 4 < 7)
                        {
                            array_push($arrayPeoresComentarios, $e);
                        }
                        $acumulador = 0;
                    }
                    return $arrayPeoresComentarios;
                    break;
        }

        
    }
}

?>