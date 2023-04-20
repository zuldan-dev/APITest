<?php
namespace App\Utils;

/**
 * Class CustomMethods
 * @package App\Utils
 */
class CustomMethods {
    /**
     * Rendering 404 page
     */
    public static function get404(): void {
        http_response_code('404');
        echo "<h1>404</h1>";
    }

    /**
     * Rendering correct data
     * @param array $rawData
     * @param string $token
     */
    public static function renderData( array $rawData, string $token ): void {
        $data = [
            'status' => 'Ok',
            'token' => $token,
            'data' => $rawData
        ];
        self::renderJson( $data );
    }

    /**
     * Rendering error message
     * @param string $msg
     */
    public static function renderError( string $msg ): void {
        self::renderMsg( $msg, 'Error' );
    }

    /**
     * Rendering info message
     * @param string $msg
     */
    public static function renderInfo( string $msg ): void {
        self::renderMsg( $msg );
    }

    /**
     * Rendering message
     * @param string $msg
     * @param string $status
     */
    private static function renderMsg( string $msg, string $status = 'Ok' ): void {
        $data = [
            'status' => $status,
            'data' => $msg
        ];
        self::renderJson( $data );
    }

    /**
     * Rendering JSON
     * @param array $data
     */
    private static function renderJson( array $data = [] ):void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
