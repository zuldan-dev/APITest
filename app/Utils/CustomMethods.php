<?php
namespace App\Utils;

/**
 * Class CustomMethods
 * @package App\Utils
 */
class CustomMethods {
    /**
     * Render 404 page
     */
    public static function get404(): void {
        http_response_code('404');
        echo "<h1>404</h1>";
    }

    public static function renderData( array $rawData, string $token ): void {
        $data = [
            'status' => 'Ok',
            'token' => $token,
            'data' => $rawData
        ];
        self::renderJson( $data );
    }

    public static function renderError( string $msg ): void {
        self::renderMsg( $msg, 'Error' );
    }

    public static function renderInfo( string $msg ): void {
        self::renderMsg( $msg );
    }

    private static function renderMsg( string $msg, string $status = 'Ok' ): void {
        $data = [
            'status' => $status,
            'data' => $msg
        ];
        self::renderJson( $data );
    }

    private static function renderJson( array $data = [] ):void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
