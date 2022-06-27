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

}

?>