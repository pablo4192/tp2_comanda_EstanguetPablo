<?php
require_once "./models/pedido.php";
require_once "./models/producto.php";
require_once "./models/usuario.php";
require_once "./models/manejadorArchivos.php";


class ArchivoController
{
    public function DescargarDatos_Csv($request, $response, $args)
    {
        $rutaConsulta = $request->getUri()->getPath();
        $lista;
        $rutaCsv;

        switch($rutaConsulta)
        {
            case "/descarga/pedidos":
                $lista = Pedido::Listar();
                $rutaCsv = "pedidos.csv";
                break;
                case "/descarga/productos":
                    $lista = Producto::Listar();
                    $rutaCsv = "productos.csv";
                    break;
                    case "/descarga/usuarios":
                        $lista = Usuario::Listar();
                        $rutaCsv = "usuarios.csv";
                        break;
        }

        $manejador = new ManejadorArchivos($rutaCsv);

        if($manejador->DescargarEnCsv($lista))
        {
            //$payload = json_encode(array("Mensaje" => "Los datos fueron descargados en la carpeta: datos_descargados_csv"));
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        else
        {
            //$payload = json_encode(array("Error" => "Hubo un problema al guardar en csv"));
            $response = $response->withStatus(500);
        }
        //$response->getBody()->write($payload);
        
        return $response;
    }

    public function CargarDatos_DesdeCsv($request, $response, $args)
    {
        $files = $request->getUploadedFiles();
        $rutaConsulta = $request->getUri()->getPath();
        $manejador = new ManejadorArchivos("uploaded");
        
        if(!file_exists("uploaded"))
        {
            mkdir("uploaded");
        }

        $nombreArchivo = $files['archivo_csv']->getClientFilename();
        
        $files['archivo_csv']->moveTo("uploaded/".$nombreArchivo);

        //Y si no concuerda las cabeceras y tipos de datos?? Valiidar!

        switch($rutaConsulta)
        {
            case "/cargar/pedidos":
                if($manejador->CargarPedidos_DesdeCsv($nombreArchivo))
                {
                    $payload = json_encode(array("Mensaje" => "Los datos del csv fueron insertados en la base de datos"));
                    $response = $response->withStatus(200);
                }
                else
                {
                    $payload = json_encode(array("Error" => "Hubo un problema al insertar los datos del csv en la base de datos, verifique que los id no esten repetidos"));
                    $response = $response->withStatus(500);
                }
                break;
                case "/cargar/productos":
                    if($manejador->CargarProductos_DesdeCsv($nombreArchivo))
                    {
                        $payload = json_encode(array("Mensaje" => "Los datos del csv fueron insertados en la base de datos"));
                        $response = $response->withStatus(200);
                    }
                    else
                    {   
                        $payload = json_encode(array("Error" => "Hubo un problema al insertar los datos del csv en la base de datos, verifique que los id no esten repetidos"));
                        $response = $response->withStatus(500);
                    }
                    break;
                    case "/cargar/usuarios":
                        if($manejador->CargarUsuarios_DesdeCsv($nombreArchivo))
                        {
                            $payload = json_encode(array("Mensaje" => "Los datos del csv fueron insertados en la base de datos"));
                            $response = $response->withStatus(200);
                        }
                        else
                        {
                            $payload = json_encode(array("Error" => "Hubo un problema al insertar los datos del csv en la base de datos, verifique que los id no esten repetidos"));
                            $response = $response->withStatus(500);
                        }
                        break;
        }
          
        $response->getBody()->write($payload);
        return $response;

      
    }

}



?>