<?php
use Firebase\JWT\JWT;
 
class Jwtoken
{
    private static $claveSecreta = '1234';
    private static $tipoCodificacion = ['HS256'];

    public static function CrearToken($data)
    {
        $tiempoActual = time();

        $payload = array(
            'iat' => $tiempoActual,
            //'exp' => $tiempoActual + (2000),
            'data' => $data,
            'app' => "Jwtoken::CrearToken"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function Verificar($token)
    {
        if(empty($token))
        {
            //throw new Exception("El token esta vacio");
            return null;
        }

        try
        {
            $tokenDecodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoCodificacion
            );
             
            $dataToken = $tokenDecodificado->data;
            $dataObj = json_decode($dataToken);
            
            return  $dataObj;
        }
        catch(Exception $ex)
        {
            throw $ex;
        }
    }
}

?>