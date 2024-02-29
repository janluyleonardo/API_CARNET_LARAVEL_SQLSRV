<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class documentController extends Controller
{
    /**
     * document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateEmailZoho(Request $request)
    {
        // $url ="https://flow.zoho.com/707796366/flow/webhook/incoming?zapikey=1001.2ced7f171a77de388c2028cf0ed3ff4c.b136e23532a0fc5dad4d1f41dc326854&isdebug=false";
        $url =env('ZOHO_URL', '');

        if($request->numero_identificacion !=null &&
        $request->correo_institucional !=null &&
        $request->correo_personal !=null)
        {
            $parametros = [
                "numero_identificacion"=> $request->numero_identificacion,
                "correo_institucional"=> $request->correo_institucional,
                "correo_personal"=> $request->correo_personal,
                "nombres"=> $request->nombres,
                "apellidos"=> $request->apellidos
            ];
            try {
                $response = Http::post($url, $parametros);
                return response()->json(['success' => 'Actualización exitosa en ZOHO'], 200);
            } catch (\Throwable $th) {
                return response()->json(["error peticion ZOHO"=>$th->getMessage()]);
            }
        }else{
            return response()->json(['error' => 'request vacio'], 400);
        }
    }

    /**
     * document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function document(Request $request)
    {
        $host = env('ORA_HOST', '');
        $port = env('ORA_PORT', '');
        $sid = env('ORA_SID', '');
        $database = env('ORA_DATABASE', '');
        $user = env('ORA_USERNAME', '');
        $passwd = env('ORA_PASSWORD', '');
        $connection_strg = $host . ":" . $port . "/" . $sid;
        //tomamos el documento que viene de la peticion
        $documento = $request->documento;
        if(isset($documento)){

            $query = "WITH Tbl1 AS (
                        SELECT DISTINCT a.est_alumno, a.cod_periodo, a.fec_creacion, g.nom_tabla, b.num_identificacion, b.nom_largo, b.dir_email, b.dir_email_per
                        FROM
                        bas_tercero b
                        INNER JOIN src_alum_programa a ON a.id_tercero = b.id_tercero
                        INNER JOIN src_generica g ON g.cod_tabla = a.est_alumno and g.tip_tabla = 'ESTALU'
                        WHERE
                        a.est_alumno IN (1, 0)
                        AND a.cod_periodo NOT LIKE '%I%'
                    ), Tbl2 AS(
                        SELECT ROW_NUMBER () OVER (PARTITION BY num_identificacion  ORDER BY fec_creacion DESC) conta,
                        est_alumno, COD_PERIODO, nom_tabla, num_identificacion, nom_largo, dir_email, dir_email_per
                        FROM
                        Tbl1
                    ), Tbl3 AS (
                        SELECT
                        nom_tabla AS \"Estado del Alumno\"
                        , num_identificacion AS \"Identificación\"
                        , nom_largo AS \"Nombre\"
                        , dir_email AS \"Correo Institucional\"
                        , dir_email_per AS \"Correo Personal\"
                        FROM Tbl2
                        WHERE conta=1
                    )
                    SELECT
                    *
                    FROM
                    Tbl3
                    WHERE
                    \"Identificación\" = '".$documento."'";

            try {
                $db_conn_orcl = oci_connect($user, $passwd, $connection_strg, 'AL32UTF8');
                $consult = oci_parse($db_conn_orcl, $query);
                oci_execute($consult);
            } catch (\Throwable $th) {
                $msg = "#33 No es posible conectar con la base de datos {$th->getMessage()}";
                return response(['error' => true, 'error-msg' => $msg], 503);
            }

            $respuesta = [];
            while ($row = oci_fetch_array($consult, OCI_ASSOC + OCI_RETURN_NULLS)) {
                //aseguramos que si el valor llega nulo se reemplace por vacio
                $estado = $row['Estado del Alumno'] == 'A' ? 'ACTIVO' : 'INACTIVO';
                $identificacion = $row['Identificación'] == NULL ? 'NO REGISTRA' : $row['Identificación'];
                $nombre = $row['Nombre'] == NULL ? 'NO REGISTRA' : $row['Nombre'];
                $email_institucional = $row['Correo Institucional'] == NULL ? 'NO REGISTRA' : $row['Correo Institucional'];
                $email_personal = $row['Correo Personal'] == NULL ? 'NO REGISTRA' : $row['Correo Personal'];
                $datos = [
                    'estado' => $estado,
                    'identificacion' => $identificacion,
                    'nombre' => $nombre,
                    'correoInstitucional' => $email_institucional,
                    'correoPersonal' => $email_personal,
                ];
            }
            //liberamos la conexion para que no se quede operando en db y genere funcionamientos errados
            oci_free_statement($consult);
            oci_close($db_conn_orcl);
            //validamos que la respuesta contenga datos para retornar en el json sino cargamos 404
            if ($datos != null) {
                return response()->json($datos, 200);
                // return response($respuesta, 200);
            } else {
                $msg = "No se encontraron resultados para el documento solicitado";
                return response(['error' => true, 'error-msg' => $msg], 404);
            }
        }else{
            $msg = "Documento llega vacío";
            return response(['error' => true, 'error-msg' => $msg], 404);
            return "vacio";
        }
    }

    /**
     * ResetPassword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ResetPassword(Request $request)
    {
        // OBTENER CORREO CORREO PARA RESETEO DE CONTRASEÑA
        $correo = $request->email;
        // OBTENER VARIABLES DE APUNTAMIENTO PARA ACCESO DE APITOKEN
        $api_url_base = env('API_URL','');
        $api_url_peticion = $api_url_base.'?email='.$correo;
        $api_user = env('API_USER','');
        $api_pass = env('API_PASS','');
        try {
            // PETICIÓN GET CON AUTORIZACIÓN BASIC AUTH
            $response = Http::withBasicAuth($api_user , $api_pass)->get($api_url_peticion);
            // OBTENER EL CUERPO DE LA RESPUESTA FORMATO JSON
            $responseData = $response->json();
            // RETORNAR LA RESPUESTA O REALIZAR ACCIONES ADICIONALES
            return response()->json($responseData);
        } catch (\Exception $e) {
            // CAPTURA CUALQUIER EXCEPCIÓN QUE PUEDA OCURRIR DURANTE LA SOLICITUD
            return response()->json(['ERROR-CONSUMO-RESETPASSWD' => $e->getMessage()], 500);
        }
    }
}
