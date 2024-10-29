<?php

use App\Http\Controllers\FacturasController;
use App\Http\Controllers\NoConformidadSinRecepcionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {

    $browser = new HttpBrowser(HttpClient::create());

    // Realizar una solicitud GET a la página que queremos scrapear
    $crawler = $browser->request('GET', 'https://www.valor-dolar.cl/CLP_GBP');

    // Buscar el div con el id 'exchange-main-description' y obtener su texto
    $description = $crawler->filter('#exchange-main-description')->text();

    // Devolver el contenido como respuesta
    return response()->json(['description' => $description]);

   /*  $url = 'https://bailac.bcnworkflow.com/api/rest.php/Documento/get';

    // Parámetros de la URL
    $params = [
        'tipoDocumento' => 'CL_DTE',
        'fechaInicio' => '2024-08-01',
        'fechaFin' => '2024-09-25',
        'tipoIngreso' => 'R'
    ];

    // Realizar la petición GET con headers personalizados
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => '2783a5b73ea6446c8b3932a026bb8343a1892f5c',
    ])->get($url, $params);

    // Verificar si la petición fue exitosa
    if ($response->successful()) {
        $data = $response->json(); // Parsear la respuesta a JSON
        return $data; // Puedes retornar los datos o hacer algo más con ellos
    } else {
        return response()->json(['error' => 'Error al obtener los datos', 'status' => $response->status()], 500);
    }
 */



});


Route::get('/home', [HomeController::class, 'index']);
Route::get('/login', [AuthController::class, 'index']);
Route::get('/getinvoices',[FacturasController::class, 'getInvoices'])->name("getinvoices");
Route::get('/nonconformities',[NoConformidadSinRecepcionController::class, 'getAllNonConformities'])->name("getAllNonConformities");
Route::get('/findnonconformities',[NoConformidadSinRecepcionController::class, 'findNonConformities'])->name("findNonConformities");
Route::get('/sendnotificationemail',[NoConformidadSinRecepcionController::class, 'sendNotificationEmail'])->name("sendNotificationEmail");
Route::get('/get',[NoConformidadSinRecepcionController::class, 'getAllNonConformities'])->name("getAllNonConformities");
Route::get('/marknonconformanceclosed',[NoConformidadSinRecepcionController::class, 'markNonConformanceClosed'])->name("markNonConformanceClosed");



