<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiBcnService
{
    protected $token;

    public function login()
    {
        $url = 'https://bailac.bcnworkflow.com/api/rest.php/Login/getToken';

        // Credenciales para la petición de login
        $credentials = [
            'ambiente_id' => 22,
            'empresa_id' => 57,
            'usuario' => 'api',
            'contrasena' => 'B41l4c!..2024',
        ];

        // Hacer la petición POST con el body en formato JSON
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($url, $credentials);

        // Verificar si la petición fue exitosa
        if ($response->successful()) {
            $this->token = $response->json()['token']; // Ajustar el nombre del campo si es diferente
        } else {
            // Manejar el error en caso de fallo
            throw new \Exception('Error al obtener el token: ' . $response->body());
        }

        return $this->token;
    }

    public function getInvoices($params)
    {
        $url = 'https://bailac.bcnworkflow.com/api/rest.php/Documento/get';

        // Si no hay token, hacer login
        if (!$this->token) {
            $this->login();
        }

        // Hacer la petición GET con el token
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $this->token,
        ])->get($url, $params);

        return $response->json();
    }
}

?>
