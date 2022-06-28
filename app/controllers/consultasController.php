<?php

require_once "./models/consultas.php";


class ConsultasController
{
    private function RetornarDataToken($dataRequest)
    {
        $header = $dataRequest->getHeaderLine('Authorization');

        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {   
            $token = "";
        }

        $dataToken = Jwtoken::Verificar($token);

        return $dataToken;

    }

    public function RetornarLogins($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consultas = new Consultas();
        
        $logins = $consultas->ConsultarLogins();
        Usuario::RegistrarOperacion($dataToken, "listado logins");

        if(count($logins) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay logins en la base de datos"));
        }
        else
        {
            $payload = json_encode(array("listado_logins" => $logins));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;

    }

    public function RetornarLoginsParam($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);

        $dataToken = $this->RetornarDataToken($request);
        
        $consultas = new Consultas();
        $logins = $consultas->ConsultarLoginsParam($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado logins con parametros");

        if(count($logins) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay logins en la base de datos entre las fechas pasadas como parametros"));
        }
        else
        {
            $payload = json_encode(array("listado_logins" => $logins));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperacionesPorSector($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $puesto = $data['puesto'];
        $consultas = new Consultas();
        
        $operacionesPorSector = $consultas->ConsultarOperacionesPorSector($puesto);
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por sector");

        if(count($operacionesPorSector) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del sector ".$puesto." en la base de datos"));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorSector));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperacionesPorSectorParam($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $puesto = $data['puesto'];
        $params = json_decode($data['parametros']);
        $consultas = new Consultas();
        
        $operacionesPorSector = $consultas->ConsultarOperacionesPorSectorParam($puesto, $params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por sector con parametros");

        if(count($operacionesPorSector) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del sector ".$puesto." en la base de datos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorSector));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperacionesPorSector_PorEmpleado($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $puesto = $data['puesto'];
       
        $consultas = new Consultas();
        
        $operacionesPorSector = $consultas->ConsultarOperacionesPorSector_PorEmpleado($puesto);
    
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por sector listado por empleado");

        if(count($operacionesPorSector) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del sector ".$puesto." en la base de datos"));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorSector));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperacionesPorSector_PorEmpleadoParam($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $puesto = $data['puesto'];
        $params = json_decode($data['parametros']);
       
        $consultas = new Consultas();
        
        $operacionesPorSector = $consultas->ConsultarOperacionesPorSector_PorEmpleadoParam($puesto, $params->desde, $params->hasta);
    
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por sector listado por empleado con parametros");

        if(count($operacionesPorSector) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del sector ".$puesto." en la base de datos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorSector));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperaciones_PorIdEmpleado($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getQueryParams();
        $id = $data['id'];
        
        $consultas = new Consultas();
        
        $operacionesPorId = $consultas->ConsultarOperaciones_PorIdEmpleado($id);
    
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por id empleado");

        if(count($operacionesPorId) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del empleado id ".$id." en la base de datos"));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorId));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarOperaciones_PorIdEmpleadoParam($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $id = $data['id'];
        $params = json_decode($data['parametros']);
        
        $consultas = new Consultas();
        
        $operacionesPorId = $consultas->ConsultarOperaciones_PorIdEmpleadoParam($id, $params->desde, $params->hasta);
    
        Usuario::RegistrarOperacion($dataToken, "listado operaciones por id empleado con parametros");

        if(count($operacionesPorId) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay operaciones registradas del empleado id ".$id." en la base de datos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_operaciones" => $operacionesPorId));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMasVendido($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $productosMasVendidos = $consulta->ConsultarMasVendido();
        Usuario::RegistrarOperacion($dataToken, "listado productos mas vendidos");

        if(count($productosMasVendidos) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron productos vendidos"));
        }
        else
        {
            $payload = json_encode(array("listado_productosMasVendidos" => $productosMasVendidos));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMasVendido_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $productosMasVendidos = $consulta->ConsultarMasVendido_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado productos mas vendidos con parametros");

        if(count($productosMasVendidos) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron productos vendidos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_productosMasVendidos" => $productosMasVendidos));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMenosVendido($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $productosMenosVendidos = $consulta->ConsultarMenosVendido();
        Usuario::RegistrarOperacion($dataToken, "listado productos menos vendidos");

        if(count($productosMenosVendidos) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron productos vendidos"));
        }
        else
        {
            $payload = json_encode(array("listado_productosMenosVendidos" => $productosMenosVendidos));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMenosVendido_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $productosMenosVendidos = $consulta->ConsultarMenosVendido_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado productos menos vendidos con parametros");

        if(count($productosMenosVendidos) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron productos vendidos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_productosMenosVendidos" => $productosMenosVendidos));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarPedidosFueraDeTiempo($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $pedidosFueraDeTiempo = $consulta->ConsultarPedidosFueraDeTiempo();
        Usuario::RegistrarOperacion($dataToken, "listado pedidos fuera de tiempo");

        if(count($pedidosFueraDeTiempo) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron pedidos entregados fuera de el tiempo estipulado"));
        }
        else
        {
            $payload = json_encode(array("listado_pedidosFueraDeTiempo" => $pedidosFueraDeTiempo));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarPedidosFueraDeTiempo_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $pedidosFueraDeTiempo = $consulta->ConsultarPedidosFueraDeTiempo_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado pedidos fuera de tiempo con parametros");

        if(count($pedidosFueraDeTiempo) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron pedidos entregados fuera de el tiempo estipulado entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_pedidosFueraDeTiempo" => $pedidosFueraDeTiempo));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarPedidosCancelados($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $pedidosCancelados = $consulta->ConsultarPedidosCancelados();
        Usuario::RegistrarOperacion($dataToken, "listado pedidos cancelados");

        if(count($pedidosCancelados) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron pedidos cancelados"));
        }
        else
        {
            $payload = json_encode(array("listado_pedidosCancelados" => $pedidosCancelados));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarPedidosCancelados_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $pedidosCancelados = $consulta->ConsultarPedidosCancelados_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado pedidos cancelados con parametros");

        if(count($pedidosCancelados) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron pedidos cancelados entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_pedidosCancelados" => $pedidosCancelados));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMasUsada($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mesasMasUsadas = $consulta->ConsultarMesaMasUsada();
        Usuario::RegistrarOperacion($dataToken, "listado mesas mas usadas");

        if(count($mesasMasUsadas) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas usadas"));
        }
        else
        {
            $payload = json_encode(array("listado_MesasMasUsadas" => $mesasMasUsadas));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMasUsada_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesasMasUsadas = $consulta->ConsultarMesaMasUsada_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas mas usadas con parametros");

        if(count($mesasMasUsadas) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron productos vendidos entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_MesasMasUsadas" => $mesasMasUsadas));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenosUsada($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mesaMenosUsada = $consulta->ConsultarMesaMenosUsada();
        Usuario::RegistrarOperacion($dataToken, "listado mesas menos usadas");

        if(count($mesaMenosUsada) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas usadas"));
        }
        else
        {
            $payload = json_encode(array("listado_MesasMenosUsadas" => $mesaMenosUsada));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenosUsada_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesaMenosUsada = $consulta->ConsultarMesaMenosUsada_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas menos usadas con parametros");

        if(count($mesaMenosUsada) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas usadas entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_MesasMenosUsadas" => $mesaMenosUsada));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMasFacturacion($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mesasConMasFacturacion = $consulta->ConsultarMesaMasFacturacion();
        Usuario::RegistrarOperacion($dataToken, "listado mesas con mas facturacion");

        if(count($mesasConMasFacturacion) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas"));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMasFacturacion" => $mesasConMasFacturacion));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMasFacturacion_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesasConMasFacturacion = $consulta->ConsultarMesaMasFacturacion_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas con mas facturacion con parametros");

        if(count($mesasConMasFacturacion) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMasFacturacion" => $mesasConMasFacturacion));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenosFacturacion($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mesasConMenosFacturacion = $consulta->ConsultarMesaMenosFacturacion();
        Usuario::RegistrarOperacion($dataToken, "listado mesas con menos facturacion");

        if(count($mesasConMenosFacturacion) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas"));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMenosFacturacion" => $mesasConMenosFacturacion));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenosFacturacion_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesasConMenosFacturacion = $consulta->ConsultarMesaMenosFacturacion_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas con menos facturacion con parametros");

        if(count($mesasConMenosFacturacion) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMenosFacturacion" => $mesasConMenosFacturacion));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMayorImporte($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mesasConMayorImporte = $consulta->ConsultarMesaMayorImporte();
        Usuario::RegistrarOperacion($dataToken, "listado mesas con mayor importe");

        if(count($mesasConMayorImporte) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas"));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMayorImporte" => $mesasConMayorImporte));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMayorImporte_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesasConMayorImporte = $consulta->ConsultarMesaMayorImporte_Param($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas con mayor importe con parametros");

        if(count($mesasConMayorImporte) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMayorImporte" => $mesasConMayorImporte));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenorImporte($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
      
        $consulta = new Consultas();

        $mesasConMenorImporte = $consulta->ConsultarMesaMenorImporte();
        Usuario::RegistrarOperacion($dataToken, "listado mesas con menor importe ");

        if(count($mesasConMenorImporte) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas"));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMenorImporte" => $mesasConMenorImporte));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMesaMenorImporte_Param($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $mesasConMenorImporte = $consulta->ConsultarMesaMenorImporte_Params($params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "listado mesas con menor importe con parametros");

        if(count($mesasConMenorImporte) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas entre las fechas ".$params->desde." y ".$params->hasta));
        }
        else
        {
            $payload = json_encode(array("listado_mesasConMenorImporte" => $mesasConMenorImporte));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarFacturacionMesa($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $data = $request->getParsedBody();
        
        if(array_key_exists("id_mesa", $data))
        {
            $id = $data['id_mesa'];
        }
        else
        {
            $id = "";
        }

        $params = json_decode($data['parametros']);
        $consulta = new Consultas();

        $facturacion = $consulta->ConsultarFacturacionMesa($id, $params->desde, $params->hasta);
        Usuario::RegistrarOperacion($dataToken, "consulta facturacion mesa");
       
        if($facturacion[0] == null)
        {
            $payload = json_encode(array("Mensaje" => "No se encontraron mesas entre las fechas ".$params->desde." y ".$params->hasta.". Verifique si el id_mesa es correcto (Parametro obligatorio para la consulta)"));
        }
        else
        {
            $payload = json_encode(array("Facturacion de la mesa id ".$id." entre las fechas ".$params->desde." y ".$params->hasta => $facturacion[0]));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarMejoresComentarios($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $mejoresComentarios = $consulta->ConsultarComentarios("mejores");
        Usuario::RegistrarOperacion($dataToken, "listado mejores comentarios");

        if(count($mejoresComentarios) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay comentarios"));
        }
        else
        {
            $payload = json_encode(array("mejoresComentarios  (promedio > a 6)" => $mejoresComentarios));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }

    public function RetornarPeoresComentarios($request, $response, $args)
    {
        $dataToken = $this->RetornarDataToken($request);
        $consulta = new Consultas();

        $peoresComentarios = $consulta->ConsultarComentarios("peores");
        Usuario::RegistrarOperacion($dataToken, "listado peores comentarios");

        if(count($peoresComentarios) == 0)
        {
            $payload = json_encode(array("Mensaje" => "No hay comentarios"));
        }
        else
        {
            $payload = json_encode(array("peoresComentarios (promedio < a 7)" => $peoresComentarios));
        }

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }


}

?>