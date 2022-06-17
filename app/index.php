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
require_once "./middlewares/verificadorParametros.php";
require_once "./controllers/loggerController.php";
require_once "./middlewares/verificadorCredenciales.php";


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//----------------------------------------------------------

//Registro-Listar Productos     //Verificado solo socios pueden ingresar productos (ver listar)
$app->group('/productos', function (RouteCollectorProxy $group){
    $group->get('[/]', \ProductoController::class . ':ListarProductos');
    $group->post('[/producto]', \ProductoController::class . ':AltaProducto')->add(\VerificadorParametros::class . ':VerificarParametrosProductoAlta');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Baja Productos //En la consulta Postman usar form-urlencoded body
$app->group('/productos/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_producto]', \ProductoController::class . ':BajaProducto')->add(\VerificadorParametros::class . ':VerificarParametrosProductoBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Modificar Productos
$app->group('/productos/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/producto]',  \ProductoController::class . ':ModificarProducto')->add(\VerificadorParametros::class . ':VerificarParametrosModificacionProducto');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//----------------------------------------------------------

//Registro-Listar Usuarios (Se valida acceso en sprint 2)
$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \UsuarioController::class . ':ListarUsuarios');
    $group->post('[/usuario]', \UsuarioController::class . ':AltaUsuario')->add(\VerificadorParametros::class . ':VerificarParametrosUsuario');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Baja usuario //En la consulta Postman usar form-urlencoded body
$app->group('/usuarios/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_usuario]', \UsuarioController::class . ':BajaUsuario')->add(\VerificadorParametros::class . ':VerificarParametrosUsuarioBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Modificar usuarios
$app->group('/usuarios/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/usuario]',  \UsuarioController::class . ':ModificarUsuario')->add(\VerificadorParametros::class . ':VerificarParametrosModificacionUsuario');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//----------------------------------------------------------

//Registro-Listar Pedidos (se relacionan con la mesa al hacer el alta)  //Verificado solo mozos pueden registrar pedidos (ver listar)
$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->get('[/]', \PedidoController::class . ':ListarPedidos');
    $group->post('[/pedido]', \PedidoController::class . ':AltaPedido')->add(\VerificadorParametros::class . ':VerificarParametrosPedido');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Baja pedido //En la consulta Postman usar form-urlencoded body
$app->group('/pedidos/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_pedido]', \PedidoController::class . ':BajaPedido')->add(\VerificadorParametros::class . ':VerificarParametrosPedidoBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Modificar pedidos
$app->group('/pedidos/modificacion', function (RouteCollectorProxy $group){
    $group->put('[/pedido]',  \PedidoController::class . ':ModificarPedido')->add(\VerificadorParametros::class . ':VerificarParametrosModificacionPedido');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Cambios de estado de los pedidos / productos_pedidos / mesas
$app->group('/productos_pedidos/pendientes', function (RouteCollectorProxy $group){
    $group->get('[/]', \PedidoController::class . ':ListarProductos_PedidosPendientes'); //Me retorna los productos pendientes de preparacion segun puesto
    $group->post('[/estado]', \PedidoController::class . ':CambiarEstadoProducto_pedido')->add(\VerificadorParametros::class . ':VerificarParametrosCambiosEstadoPedidos');
    
})->add(\VerificadorCredenciales::class . ':VerificarToken');



//----------------------------------------------------------

//Registro-Listar Mesas (se instancian e insertan en la base y luego pueden ser usadas/relacionadas con el pedido)
$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->get('[/]', \MesaController::class . ':ListarMesas');
    $group->post('[/mesa]', \MesaController::class . ':AltaMesa')->add(\VerificadorParametros::class . ':VerificarParametrosMesa');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Baja mesa //En la consulta Postman usar form-urlencoded body
$app->group('/mesas/baja', function (RouteCollectorProxy $group){
    $group->delete('[/id_mesa]', \MesaController::class . ':BajaMesa')->add(\VerificadorParametros::class . ':VerificarParametrosMesaBaja');
})->add(\VerificadorCredenciales::class . ':VerificarToken');

//Modificar Mesas
//La modificacion de la mesa se hace a partir de los cambios en los pedidos, Se pueden agregar mesas o quitar mesas del comercio

//----------------------------------------------------------

//Login 
$app->group('/login', function (RouteCollectorProxy $group){    
    $group->post('[/usuario]', \LoggerController::class . ':Loguear')->add(\VerificadorCredenciales::class . ':VerificarUsuario')->add(\VerificadorParametros::class . ':VerificarParametrosLogin'); 
}); 

$app->run();

?>