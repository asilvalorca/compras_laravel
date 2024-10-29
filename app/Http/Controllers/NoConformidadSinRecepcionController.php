<?php

namespace App\Http\Controllers;

use App\Mail\NonConformitiesNotification;
use App\Services\ApiBcnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;



class noConformidadSinRecepcionController extends Controller
{


    public function calculateDaysDifference($startDate, $currentDate) {
        // Parse the provided dates
        $date1 = Carbon::parse($startDate);
        $date2 = Carbon::parse($currentDate);

        // Calculate the difference in days
        $difference = $date1->diffInDays($date2);

        // Return the difference in days
        return $difference;
    }

    public function  findNonConformities(){

        $currentDate = Carbon::now();

        $tiempo = DB::table('compras_tiempo')
                ->where('id_modulo', 13)
                ->first();


        $registros = DB::table('compras_item as i')
        ->select('i.id_sol', 'i.itemnum', 'i.aprob', 'ri.fecha_registro')
        ->join('compras_registro_item as ri', 'i.id_item', '=', 'ri.id_item') // INNER JOIN
        ->leftJoin('compras_sol_noconformidad_sin_recepcion as ncsr', function($join) {
            $join->on('ncsr.id_sol', '=', 'i.id_sol')
                 ->on('ncsr.itemnum', '=', 'i.itemnum'); // LEFT JOIN con múltiples condiciones
        })
        ->where('ri.estado_item', 71) // Condición en la tabla ri
        ->where('i.aprob', 71) // Condición en la tabla i
        ->whereNull('ncsr.id_sol') // Filtrar donde ncsr.id_sol es NULL
        ->get();

        foreach ($registros as  $value) {
            print_r($value);
            $daysCount = $this->calculateDaysDifference($value->fecha_registro, $currentDate);
            if($daysCount > $tiempo->tiempo){

                DB::table('compras_sol_noconformidad_sin_recepcion')
                    ->insert([
                        'id_sol' => $value->id_sol,
                        'itemnum' => $value->itemnum,
                        'fecha_despacho' => $value->fecha_registro,

                    ]);

            }

        }


    }
    public function markNonConformanceClosed(){

        $currentDate = Carbon::now();



        $registros = DB::table('compras_item as i')
        ->select('i.id_sol', 'i.itemnum', 'i.aprob',DB::raw("CONCAT(ri.fecha_registro,' ', ri.hora) as fecha_registro") )
        ->join('compras_registro_item as ri', 'i.id_item', '=', 'ri.id_item') // INNER JOIN
        ->join('compras_sol_noconformidad_sin_recepcion as ncsr', function($join) {
            $join->on('ncsr.id_sol', '=', 'i.id_sol')
                 ->on('ncsr.itemnum', '=', 'i.itemnum'); // LEFT JOIN con múltiples condiciones
        })
        ->where('ri.estado_item', 72) // Condición en la tabla ri
        ->where('i.aprob', 72) // Condición en la tabla i

        ->get();


        foreach ($registros as  $value) {

            print_r($value);
            $affected = DB::table('compras_sol_noconformidad_sin_recepcion')
                ->where('id_sol', $value->id_sol)
                ->where('itemnum', $value->itemnum)
                ->update(['estado' => 1, "fecha_cierre" => $value->fecha_registro]);

        }


    }

    public function getAllNonConformities(){
        return DB::table('compras_sol_noconformidad_sin_recepcion')->get();
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
}
