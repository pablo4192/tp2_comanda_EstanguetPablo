<?php
//Alumno: Estanguet Pablo, Div: 3C

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once "./controllers/productoController.php";
require_once "./controllers/usuarioController.php";
require_once "./controllers/pedidoController.php";
require_once "./controllers/mesaController.php";
require_once "./controllers/loggerController.php";
require_once "./controllers/archivoController.php";
require_once "./controllers/consultasController.php";
require_once "./middlewares/verificadorParametrosUsuario.php";
require_once "./middlewares/verificadorParametrosProducto.php";
require_once "./middlewares/verificadorParametrosPedido.php";
require_once "./middlewares/verificadorParametrosMesa.php";
require_once "./middlewares/verificador.php";

require_once "./middlewares/verificadorCredenciales.php";

date_default_timezone_set("America/Argentina/Buenos_Aires");

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//REGISTRO-LISTAR PRODUCTOS    
$app->group('/productos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ProductoController::class . ':ListarProductos');
    $group->post('[/producto]', \ProductoController::class . ':AltaProducto')->add(\VerificadorParametrosProducto::class . ':VerificarAlta');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//BAJA PRODUCTOS //En la consulta Postman usar form-urlencoded body
$app->group('/productos/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_producto]', \ProductoController::class . ':BajaProducto')->add(\VerificadorParametrosProducto::class . ':VerificarBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//MODIFICAR PRODUCTOS
$app->group('/productos/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/producto]',  \ProductoController::class . ':ModificarProducto')->add(\VerificadorParametrosProducto::class . ':VerificarModificacion');
})->add(\VerificadorCredenciales::class . ':VerificarToken');


//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//REGISTRO-LISTAR USUARIOS
$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \UsuarioController::class . ':ListarUsuarios');
    $group->post('[/usuario]', \UsuarioController::class . ':AltaUsuario')->add(\VerificadorParametrosUsuario::class . ':VerificarAlta');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//BAJA USUARIO //En la consulta Postman usar form-urlencoded body
$app->group('/usuarios/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_usuario]', \UsuarioController::class . ':BajaUsuario')->add(\VerificadorParametrosUsuario::class . ':VerificarBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//MODIFICAR USUARIO
$app->group('/usuarios/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/usuario]',  \UsuarioController::class . ':ModificarUsuario')->add(\VerificadorParametrosUsuario::class . ':VerificarModificacion');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//SUSPENDER
$app->group('/usuarios/suspender', function (RouteCollectorProxy $group){
   $group->post('[/id_usuario]', \UsuarioController::class . ':SuspenderUsuario');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//LEVANTAR SUSPENSION
$app->group('/usuarios/levantarSuspension', function (RouteCollectorProxy $group){
    $group->post('[/id_usuario]', \UsuarioController::class . ':LevantarSuspensionUsuario');
 })->add(\VerificadorCredenciales::class . ':VerificarToken');

//-----------------------------------------------------------------------------------------------------------------------------------------------------------


//REGISTRO-LISTAR PEDIDOS (se relacionan con la mesa al hacer el alta, si la misma esta disponible) 
$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->get('[/]', \PedidoController::class . ':ListarPedidos');
    $group->post('[/pedido]', \PedidoController::class . ':AltaPedido')->add(\VerificadorParametrosPedido::class . ':VerificarAlta');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//BAJA PEDIDO //En la consulta Postman usar form-urlencoded body
$app->group('/pedidos/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_pedido]', \PedidoController::class . ':BajaPedido')->add(\VerificadorParametrosPedido::class . ':VerificarBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//MODIFICAR PEDIDOS
$app->group('/pedidos/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/pedido]',  \PedidoController::class . ':ModificarPedido')->add(\VerificadorParametrosPedido::class . ':VerificarModificacion');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//CAMBIOS DE ESTADO DE LOS PEDIDOS/ PRODUCTOS_PEDIDOS / MESAS
$app->group('/productos_pedidos/pendientes', function (RouteCollectorProxy $group){
    $group->get('[/]', \PedidoController::class . ':ListarProductos_PedidosPendientes'); //Me retorna los productos pendientes de preparacion segun puesto, analiza el token
    $group->post('[/estado]', \PedidoController::class . ':CambiarEstadoProducto_pedido')->add(\VerificadorParametrosPedido::class . ':VerificarCambiosEstado');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//CANCELAR PEDIDO
$app->group('/pedidos/cancelar', function (RouteCollectorProxy $group){
    $group->post('[/estado]', \PedidoController::class . ':Cancelarpedido')->add(\VerificadorParametrosPedido::class . ':VerificarCancelacion');
})->add(\VerificadorCredenciales::class . ':VerificarToken');
   
//ENDPOINT PARA LISTAR LOS PEDIDOS 'LISTOS PARA SERVIR'
$app->group('/pedidos/listos', function (RouteCollectorProxy $group){
    $group->get('[/]', \PedidoController::class . ':ListarPedidosListos'); 
    
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//CLIENTE CONSULTA EL TIEMPO DE ESPERA DE SU PEDIDO
$app->group('/pedidos/cliente', function (RouteCollectorProxy $group){
    $group->post('[/info]', \PedidoController::class . ':RetornarTiempoDeEspera');
})->add(\VerificadorParametrosPedido::class . ':VerificarTiempoEspera');


//-----------------------------------------------------------------------------------------------------------------------------------------------------------


//REGISTRO-LISTAR MESAS (se instancian e insertan en la base y luego pueden ser usadas/relacionadas con el pedido)
$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->get('[/]', \MesaController::class . ':ListarMesas');
    $group->post('[/mesa]', \MesaController::class . ':AltaMesa')->add(\VerificadorParametrosMesa::class . ':VerificarAlta');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//BAJA MESA //En la consulta Postman usar form-urlencoded body
$app->group('/mesas/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_mesa]', \MesaController::class . ':BajaMesa')->add(\VerificadorParametrosMesa::class . ':VerificarBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//MODIFICAR MESAS
//La modificacion de la mesa se hace a partir de los cambios en los pedidos, Se pueden agregar mesas o quitar mesas del comercio

//CAMBIAR ESTADO MESA 
//El mozo le lleva el pedido, la mesa termina de comer el mozo le cobra, el socio cierra la mesa y vuelve a estar disponible (cerrada), se habilita la encuesta
$app->group('/mesas/cambiosEstado', function (RouteCollectorProxy $group){
    $group->post('[/entregar]', \MesaController::class . ':EntregarPedido');
    $group->put('[/cobrar]', \MesaController::class . ':CobrarPedido');
    $group->delete('[/cerrar]', \MesaController::class . ':CerrarMesa');
})->add(\VerificadorCredenciales::class . ':VerificarToken')->add(\VerificadorParametrosMesa::class . ':VerificarCambiosEstado');

//ENCUESTA DE SATISFACCION
$app->group('/encuesta', function (RouteCollectorProxy $group){
    $group->post('[/]', \MesaController::class . ':RealizarEncuesta'); 
})->add(\VerificadorParametrosMesa::class . ':VerificarParametrosEncuesta');


//-----------------------------------------------------------------------------------------------------------------------------------------------------------


//DESCARGA DE DATOS BD A UN ARCHIVO .CSV (sin token ya que se guarda en el front)
$app->group('/descarga/pedidos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Csv');
});

$app->group('/descarga/productos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Csv');
});

$app->group('/descarga/usuarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Csv');
});

//DESCARGA DE DATOS BD A UN ARCHIVO PDF 
$app->group('/descarga_pdf/pedidos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Pdf');
});

$app->group('/descarga_pdf/productos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Pdf');
});

$app->group('/descarga_pdf/usuarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \ArchivoController::class . ':DescargarDatos_Pdf');
});


//CARGA DE DATOS DESDE UN ARCHIVO .CSV A BD (solo socio)
$app->group('/cargar/pedidos', function (RouteCollectorProxy $group){
    $group->post('[/archivo_csv]', \ArchivoController::class . ':CargarDatos_DesdeCsv')->add(\Verificador::class . ':VerificarParametrosArchivos');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/cargar/usuarios', function (RouteCollectorProxy $group){
    $group->post('[/archivo_csv]', \ArchivoController::class . ':CargarDatos_DesdeCsv')->add(\Verificador::class . ':VerificarParametrosArchivos');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/cargar/productos', function (RouteCollectorProxy $group){
    $group->post('[/archivo_csv]', \ArchivoController::class . ':CargarDatos_DesdeCsv')->add(\Verificador::class . ':VerificarParametrosArchivos');
})->add(\VerificadorCredenciales::class . ':VerificarToken');


//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//CONSULTAS DEL ADMINISTRADOR (consultas sql)

//USUARIOS
$app->group('/consultar/logins', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarLogins');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarLoginsParam')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorPuesto');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/operacionesPorSector', function (RouteCollectorProxy $group){
    $group->post('[/]', \ConsultasController::class . ':RetornarOperacionesPorSector')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorPuesto');
    $group->put('[/]', \ConsultasController::class . ':RetornarOperacionesPorSectorParam')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorPuesto')->add(\Verificador::class . ':VerificarFechasConsultas');
   
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/operacionesPorSector_PorEmpleado', function (RouteCollectorProxy $group){
    $group->post('[/]', \ConsultasController::class . ':RetornarOperacionesPorSector_PorEmpleado')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorPuesto');
    $group->put('[/parametros]', \ConsultasController::class . ':RetornarOperacionesPorSector_PorEmpleadoParam')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorPuesto')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/operaciones_PorIdEmpleado', function (RouteCollectorProxy $group){
    $group->get('[/id]', \ConsultasController::class . ':RetornarOperaciones_PorIdEmpleado')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorId');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarOperaciones_PorIdEmpleadoParam')->add(\Verificador::class . ':VerificarConsultaOperaciones_PorId')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//PEDIDOS
$app->group('/consultar/masVendido', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMasVendido');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMasVendido_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/menosVendido', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMenosVendido');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMenosVendido_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/fueraDeTiempo', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarPedidosFueraDeTiempo');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarPedidosFueraDeTiempo_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/pedidosCancelados', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarPedidosCancelados');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarPedidosCancelados_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//MESAS
$app->group('/consultar/mesaMasUsada', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMasUsada');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMasUsada_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaMenosUsada', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMenosUsada');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMenosUsada_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaConMasFacturacion', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMasFacturacion');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMasFacturacion_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaConMenosFacturacion', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMenosFacturacion');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMenosFacturacion_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaMayorImporte', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMayorImporte');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMayorImporte_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaMenorImporte', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMesaMenorImporte');
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarMesaMenorImporte_Param')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mesaFacturacion', function (RouteCollectorProxy $group){
    $group->post('[/parametros]', \ConsultasController::class . ':RetornarFacturacionMesa')->add(\Verificador::class . ':VerificarFechasConsultas');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/mejoresComentarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarMejoresComentarios');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

$app->group('/consultar/peoresComentarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \ConsultasController::class . ':RetornarPeoresComentarios');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//-----------------------------------------------------------------------------------------------------------------------------------------------------------


//LOGIN 
$app->group('/login', function (RouteCollectorProxy $group){    
    $group->post('[/usuario]', \LoggerController::class . ':Loguear')->add(\VerificadorCredenciales::class . ':VerificarUsuario')->add(\VerificadorParametrosUsuario::class . ':VerificarLogin'); 
}); 


$app->run();

?>