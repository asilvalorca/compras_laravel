<?php

namespace App\Http\Controllers;

use App\Models\AprobacionFactura;
use App\Services\ApiBcnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FacturasController extends Controller{


    protected $apiService;

    public function __construct(ApiBcnService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getInvoicesApi(){


        $currentDate = now();
        $dateMinus15Days = now()->subDays(20); // Subtract 15 days from the current date

        $currentDateFormatted = $currentDate->format('Y-m-d');
        $startDateFormatted = $dateMinus15Days->format('Y-m-d');


        $params = [
            'tipoDocumento' => 'CL_DTE',
            /* 'fechaInicio' => '2024-08-01',
            'fechaFin' => '2024-10-17', */
            'fechaInicio' => $startDateFormatted,
            'fechaFin' => $currentDateFormatted,
            'tipoIngreso' => 'R'
        ];

        $documentos = $this->apiService->getInvoices($params);

       // return response()->json($documentos);

        return $documentos;
    }


    public function getInvoices(){
        $documentos = $this->getInvoicesApi();

      //return response()->json($documentos);
         foreach ($documentos['resp'] as $key => $value) {

            if($value['doc_propiedades_match_oc']!='0'){

                $fechaAprobacionSii = isset($value['fecha_aprobacion_sii'])
                ? Carbon::parse($value['fecha_aprobacion_sii'])->format('Y-m-d H:i:s')
                : null;

                $numeroFactura = $value['doc_folio_numero'] ?? null;
                $rut = $value['doc_emisor_fiscalid'] ?? null;
                $tipoDocumento = $value['doc_subtipo_codigo'] ?? null;

                $facturaExistente = AprobacionFactura::where('numero_factura', $numeroFactura)
                    ->where('rut', $rut)
                    ->where('tipo_documento', $tipoDocumento)
                    ->first();
                echo "</br>".$numeroFactura."--".$value['doc_propiedades_ley19983_estado'];

                if ($facturaExistente and ($value['doc_propiedades_ley19983_estado']!=null and $value['doc_propiedades_ley19983_estado']!="")) {
                    // Si la factura ya existe, actualizarla
                    $facturaExistente->update([

                        'aprobacion_sii'       => $value['doc_propiedades_ley19983_estado']? 1 : 0,
                        'fecha_aprobacion_sii' => isset($value['doc_propiedades_ley19983_fecha'])
                                                    ? Carbon::parse($value['doc_propiedades_ley19983_fecha'])->format('Y-m-d H:i:s')
                                                    : null,

                    ]);
                } else if(!$facturaExistente and !$value['doc_propiedades_ley19983_estado']) {
                    // Si la factura no existe, insertarla
                    AprobacionFactura::create([
                        /* 'numero_factura'       => $numeroFactura,
                        'rut'                  => $rut,
                        'numero_oc'            => $value['doc_propiedades_listado_oc'] ?? null,
                        'monto'            => $value['doc_monto_neto'] ?? null,
                        'fecha_emision'        => $value['doc_fecha_emision'] ?? null,
                        'fecha_vencimiento'    => $value['doc_fecha_vencimiento'] ?? null,
                        'aprobacion_sii'       => $value['doc_propiedades_ley19983_estado']? 1 : 0,
                        'fecha_aprobacion_sii' => null,
                        'porcentaje_aprobacion'=> $value['porcentaje_aprobacion'] ?? null,
                        'item_aprobados'       => $value['item_aprobados'] ?? null,
                        'tipo_documento'                => $value['doc_subtipo_codigo'],
                        'fecha_creacion'       => now(), */

                        'numero_factura'         => $numeroFactura,
                        'rut'                    => $rut,
                        'numero_oc'              => $value['doc_propiedades_listado_oc'] ?? null,
                        'monto'                  => $value['doc_monto_neto'] ?? null,
                        'fecha_emision'          => $value['doc_fecha_emision'] ?? null,
                        'fecha_vencimiento'      => $value['fecha_vencimiento'] ?? null,
                        'aprobacion_sii'         => $value['aprobacion_sii'] ?? 0,
                        'fecha_aprobacion_sii'   => null,
                        'tipo_documento'         => $value['doc_subtipo_codigo'],
                        'fecha_creacion'         => now(),
                        'aprobacion_encargado'   => 0,
                        'comentario'             => '',
                        'id_aprobador'           => 0,

                    ]);
                }

              /*   AprobacionFactura::create(
                    [
                        'numero_factura' => $value['doc_folio_numero'], // Coincidencia por número de factura
                        'rut'            => $value['doc_emisor_fiscalid'], // Coincidencia por RUT
                        'tipo_documento'  => $value['doc_subtipo_codigo'],
                    ],
                    [
                        'tipo_documento'                => $value['doc_subtipo_codigo'],
                        'numero_oc'            => $value['doc_propiedades_listado_oc'] ?? null,
                        'fecha_emision'        => $value['doc_fecha_emision'] ?? null,
                        'fecha_vencimiento'    => $value['doc_fecha_vencimiento'] ?? null,
                        'aprobacion_sii'       => $value['doc_propiedades_ley19983_estado']? 1 : 0,
                        'fecha_aprobacion_sii' => $fechaAprobacionSii,
                        'porcentaje_aprobacion'=> 0 ?? null,
                        'item_aprobados'       => 0 ?? null,
                        'fecha_creacion'       => now(), // Puedes usar una fecha de la API si es necesario
                    ]
                ); */


           /*  echo $value['doc_emisor_fiscalid']."-"
                .$value['doc_folio_numero']."-"
                .$value['doc_fecha_emision']."-"
                .$value['doc_fecha_vencimiento']."-"
                .$value['doc_propiedades_match_oc']."-"
                .$value['doc_propiedades_listado_oc']."-"
                .$value['doc_propiedades_ley19983_estado']."-"
                .$value['doc_propiedades_ley19983_fecha']."-"
                .$value['doc_propiedades_ley19983_dias']."-"
                .'<br>'; */

            }
        }


    }

    public function sendNotificationEmail(){
        $currentDate = Carbon::now();
        $registros = DB::table('compras_sol_noconformidad_sin_recepcion as ncsr')
                ->select('ncsr.id_sol', 'ncsr.itemnum',  'sc.id_user','u.tipouser','u.nombre', 'an.anegocio', 'an.ADMINISTRADOR'
                    , 'an.GERENTE','an.SUBGERENTE', 'u.correo', 'ci.subcuenta', 'ci.descrip', 'ci.cant', 'an.id as idAnegocio', 'ncsr.fecha_despacho')
                ->join('compras_sol_compra as sc', 'ncsr.id_sol', '=', 'sc.id_sol')
                ->leftJoin('compras_item as ci', function($join) {
                    $join->on('ncsr.id_sol', '=', 'ci.id_sol')
                        ->on('ncsr.itemnum', '=', 'ci.itemnum');
                })
                ->leftJoin('compras_usuarios as u', 'u.id_user', '=', 'sc.id_user')
                ->leftJoin('compras_anegocio as an', 'sc.id_anegocio', '=', 'an.id')
                ->where('ncsr.estado', 0)
                ->orderBy('an.anegocio', 'asc')
                ->orderBy('u.id_user', 'asc')
                ->get();


        if(count($registros ) == 0){
            return null;

        }
        $items = array();
        $idAnegocio = $registros[0]->idAnegocio;
        $idUser = $registros[0]->id_user;
        $bcc = 'andres.silva@bailac.cl';
        $correo = 'andres.silva@bailac.cl';
        foreach ($registros as  $value) {
            /* print_r($value); */

            try {
                /* $correo = $value->correo; */
                $mail = Mail::to($correo);
                if( $idAnegocio != $value->idAnegocio or $idUser != $value->id_user){

                   /*  echo response()->json($items, 200);
                    echo "</br></br>"; */
                    /* print_r($items); */
                    if(count($items) > 0){

                       /*  echo '</br></br><pre>';
                        print_r($items);
                        echo '</pre>'; */
                        $mail->cc("rodolfo.zalavari@bailac.cl");
                        $mail->send(new NonConformitiesNotification( $items));
                    }

                    $items = array();
                }

                $daysCount = $this->calculateDaysDifference($value->fecha_despacho, $currentDate);
                 $value->dias = $daysCount;

                $idAnegocio = $value->idAnegocio;
                $idUser = $value->id_user;


                $administrador = null;
                $subgerente = null;
                $gerente = null;
                $tipoUser = $value->tipouser;

                $mail->bcc($bcc);

                if($value->ADMINISTRADOR > 0){
                    $administrador = DB::table('compras_usuarios as u')
                                ->where('u.id_user', $value->ADMINISTRADOR)
                                ->first();

                }
                if($value->SUBGERENTE > 0){
                    $subgerente = DB::table('compras_usuarios as u')
                                ->where('u.id_user', $value->SUBGERENTE)
                                ->first();

                }
                if($value->GERENTE > 0){
                    $gerente = DB::table('compras_usuarios as u')
                                ->where('u.id_user', $value->GERENTE)
                                ->first();

                }
                $usuariosAprobadores = array("SU", 'Subgerente', 'Gerente', 'Gerentegeneral' );
                if(!in_array($tipoUser, $usuariosAprobadores)){

                    if($administrador and $administrador->id_user != $value->id_user){

                       // $mail->cc($administrador->correo);
                    }
                    if($subgerente) {

                       // $mail->cc($subgerente->correo);
                    }
                    if($gerente){
                        //$mail->cc($gerente->correo);
                    }
                }

                if($tipoUser=='Subgerente'){

                    $mail->cc($gerente->correo);

                }

                $value->administrador = $administrador? $administrador->correo : null;
                $value->subgerente = $subgerente? $subgerente->correo : null;
                $value->gerente = $gerente? $gerente->correo : null;
                $items[] =$value;

                //$mail->send(new NonConformitiesNotification( $value));
                DB::table('compras_sol_noconformidad_sin_recepcion')
                ->where('id_sol', $value->id_sol)
                ->where('itemnum', $value->itemnum)
                ->update(['estado_correo' => 1, 'fecha_correo' => Carbon::now()]);

            } catch (\Exception $e) {

                echo $e->getMessage();
                //return response()->json(['error' => 'Mail could not be sent', 'message' => $e->getMessage()], 500);
            }

        }
    }



    //
}
