<?php
require_once "pedido.php";
require_once "mesa.php";
require_once "producto.php";
require_once "usuario.php";

class ManejadorArchivos
{
    public $ruta;

    public function __construct($ruta="")
    {
        $this->ruta = $ruta;
    }

    public function DescargarEnCsv($listaADescargar)
    {
        $retorno;
        $archivo;

        if(!file_exists("datos_descargados_csv"))
        {
            mkdir("datos_descargados_csv");
        }

        switch($this->ruta)
        {
            case "pedidos.csv":
                $this->ruta = "datos_descargados_csv/pedidos.csv";
                
                $archivo = fopen($this->ruta, "w");
                
                fwrite($archivo, "id;nombre cliente;id mozo;id mesa;total;fecha;tiempo estimado;hora ingreso;hora egreso;estado;medio de pago".PHP_EOL);
                foreach($listaADescargar as $p)
                {
                    $retorno = fwrite($archivo, "$p->id;$p->nombre_cliente;$p->id_mozo;$p->id_mesa;$p->total;$p->fecha;$p->tiempo_estimado;$p->hora_ingreso;$p->hora_egreso;$p->estado;$p->medio_de_pago".PHP_EOL);
                }
                
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename=pedidos.csv');
                readfile("./datos_descargados_csv/pedidos.csv");
                break;
                case "productos.csv":
                    $this->ruta = "datos_descargados_csv/productos.csv";
                    
                    $archivo = fopen($this->ruta, "w");
                    
                    fwrite($archivo, "id;nombre;precio;stock;tipo;tiempo preparacion;ingresado por id".PHP_EOL);
                    foreach($listaADescargar as $p)
                    {
                        $retorno = fwrite($archivo, "$p->id;$p->nombre;$p->precio;$p->stock;$p->tipo;$p->tiempo_preparacion;$p->ingresado_por_id".PHP_EOL);
                    }
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename=productos.csv');
                    readfile("./datos_descargados_csv/productos.csv");
                    break;
                    case "usuarios.csv":
                        $this->ruta = "datos_descargados_csv/usuarios.csv";
                        
                        $archivo = fopen($this->ruta, "w");
                        
                        fwrite($archivo, "id;nombre;apellido;clave;puesto".PHP_EOL);
                        foreach($listaADescargar as $u)
                        {
                            $retorno = fwrite($archivo, "$u->id;$u->nombre;$u->apellido;$u->clave;$u->puesto".PHP_EOL);
                        }
                        header('Content-Type: application/csv');
                        header('Content-Disposition: attachment; filename=usuarios.csv');
                        readfile("./datos_descargados_csv/usuarios.csv");
                        break;
        }
        fclose($archivo);
        
        if($retorno > 0)
        {
            return true;
        }
        return false;
    }

    public function CargarPedidos_DesdeCsv($nombreArchivo)
    {
        $rutaArchivoACargar = $this->ruta."/".$nombreArchivo;

        if(file_exists($rutaArchivoACargar))
        {
            $array = [];
            $arrayPedidos = [];

            $archivo = fopen($rutaArchivoACargar, "r");
            
            while(!feof($archivo))
            {
                $renglon = fgets($archivo);

                if($renglon != "" && $renglon != "nombre cliente;id mozo;id mesa;total;fecha;tiempo estimado;hora ingreso;hora egreso;estado;medio de pago".PHP_EOL)
                {
                    $array = explode(";", $renglon);
                    $p = new Pedido();
                   
                    $p->nombre_cliente = $array[0];
                    $p->id_mozo = $array[1];
                    $p->id_mesa = $array[2];
                    $p->total = $array[3];
                    $p->fecha = $array[4];
                    $p->tiempo_estimado = $array[5];
                    $p->hora_ingreso = $array[6];
                    $p->hora_egreso = $array[7];
                    $p->estado = $array[8];
                    $p->medio_de_pago = $array[9];

                    if(!Pedido::InsertarDesdeCsv($p))
                    {
                        return false;
                    }

                }
            }
                    
            fclose($archivo);
            return true;
        }
                    
        return false;

    }

    public function CargarProductos_DesdeCsv($nombreArchivo)
    {
        $rutaArchivoACargar = $this->ruta."/".$nombreArchivo;

        if(file_exists($rutaArchivoACargar))
        {
            $array = [];
            $arrayProductos = [];

            $archivo = fopen($rutaArchivoACargar, "r");
            
            while(!feof($archivo))
            {
                $renglon = fgets($archivo);

                if($renglon != "" && $renglon != "nombre;precio;stock;tipo;tiempo preparacion;ingresado por id".PHP_EOL)
                {
                    $array = explode(";", $renglon);
                    $p = new Producto();
                    
                    $p->nombre = $array[0];
                    $p->precio = $array[1];
                    $p->stock = $array[2];
                    $p->tipo = $array[3];
                    $p->tiempo_preparacion = $array[4];
                    $p->ingresado_por_id = $array[5];
                    
                   
                    if(!Producto::InsertarDesdeCsv($p))
                    {
                        return false;
                    }

                }
            }
                    
            fclose($archivo);
            return true;
        }
                    
        return false;

    }

    public function CargarUsuarios_DesdeCsv($nombreArchivo)
    {
        $rutaArchivoACargar = $this->ruta."/".$nombreArchivo;

        if(file_exists($rutaArchivoACargar))
        {
            $array = [];
            $arrayUsuarios = [];

            $archivo = fopen($rutaArchivoACargar, "r");
            
            while(!feof($archivo))
            {
                $renglon = fgets($archivo);

                if($renglon != "" && $renglon != "nombre;apellido;clave;puesto".PHP_EOL)
                {
                    $array = explode(";", $renglon);
                    $u = new Usuario();
                  
                    $u->nombre = $array[0];
                    $u->apellido = $array[1];
                    $u->clave = $array[2];
                    $u->puesto = $array[3];
                  
                    if(!Usuario::InsertarDesdeCsv($u))
                    {
                        return false;
                    }

                }
            }
                    
            fclose($archivo);
            return true;
        }
                    
        return false;

    }


    

}

?>