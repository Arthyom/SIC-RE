<?php
    
    require_once APP_PATH . 'models/sicaputils/sicaputils.php';

    class SicaprestController extends AppController
    {

        public function chart_get_data()
        {
            View::template('json');

            $metodo = $_SERVER['REQUEST_METHOD'];
            $respuesta = false;

            if ($metodo == 'POST') {
                $datos =  json_decode(file_get_contents('php://input'), true);

                $chart_data = [
                    'labels' => [ 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
                    'datasets' => [
                        [
                        'label'=> 'etiqueta ',
                        'backgroundColor' => 'rgba('.rand(0,255) .','. rand(0,255) .','. rand(0,255).','. rand(0,255) .')' ,
                        'borderColor' => 'rgba('.rand(0,255) .','. rand(0,255) .',' .rand(0,255).',' .rand(0,255) .')' ,
                        'borderWidth' => 1,
                        'data' => [
                                rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),
                                rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10)

                            ]
                        ],

                        [
                            'label'=> 'etiqueta 2',
                            'backgroundColor' => 'rgba('.rand(0,255) .','. rand(0,255) .','. rand(0,255).','. rand(0,255) .')' ,
                            'borderColor' => 'rgba('.rand(0,255) .','. rand(0,255) .',' .rand(0,255).',' .rand(0,255) .')' ,
                            'borderWidth' => 1,
                            'data' => [
                                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),
                                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10)
                            ]
                        ]
                    ]
                ];
            }

            $this->data = [ 'ok' => $respuesta, 'datos' => $datos, 'chart_data' => $chart_data ];
        }

        public function chart_get_dataset()
        {

            View::template(null);
            View::select('json');

            $this->utils = new sicaputils();
            $respuesta = false;

            if( $this->utils->peticionPorPOST() ){
                $datos = $this->utils->datosJSON();
                $dataset = [
                    'label'=> 'etiqueta 1',
                    'backgroundColor' => 'rgba('.rand(0,255) .','. rand(0,255) .','. rand(0,255).','. rand(0,255) .')' ,
                    'borderColor' => 'rgba('.rand(0,255) .','. rand(0,255) .',' .rand(0,255).',' .rand(0,255) .')' ,
                    'borderWidth' => 1,
                    'data' => [
                        rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),
                        rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10)
                    ]
                ];

                $this->data = [ 'ok' => $respuesta, 'dataset' => $dataset ];

            }

        }

        public function chart_get_information()
        {
            View::template(null);
            View::select('json');

            $this->utils = new sicaputils();
            $respuesta = false;

            if( $this->utils->peticionPorPOST() ){
                $datos = $this->utils->datosJSON();
                $data = [
                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),
                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10)
                ];
                $this->data = [ 'ok' => $respuesta, 'data' => $data ];
            }     
        }


        /**
         * crear una grafica con un segmento de sql enviado y otros parametros
         * pasados vias json
         * labels, titulo, colores, incluso el tipo de grafico
         */
        public function chart_create_from()
        {
            View::template(null);
            View::select('json');

            $this->utils = new sicaputils();
            $respuesta = false;

            if( $this->utils->peticionPorPOST() ){
                $datos = $this->utils->datosJSON();

                //$buscador = new wflsempty();

                $resource = $datos['resource'];
                $label = $datos['label'];
                $labels = $datos['labels'];
                $type = $datos['type'];
                $title = $datos['title'];
                $colors = $datos['colors'];
                $query = $datos['query'];
                $data = [
                    'labels'=> $labels,
                    'datasets'=> [
                        [
                        'label' => $label,
                        'data' => [],
                        'backgroundColor' => $colors ,
                        'borderColor' => 'red',
                        'borderWidth' => 1,

                        ]
                    ]
                ];

                $d = [];

                $recurso = new $resource();



                /*ejecutar sentencias enviadas*/
                /* se podria crear una nueva serie de datos de manera iterativa */
                foreach ($labels as $i => $label) {
                    $sql = $query . $label;
                   // $el = $recurso->find_all_by_sql( $sql );
                    $ce = $recurso->count_by_sql( $sql );
                    array_push($d, $ce);
                }

                $data['datasets'][0]['data'] = $d;
             /*   $data = [
                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),
                    rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10),rand(0, 10)
                ];*/
                $this->data = [ 'vals' => $data, 'ok' => $respuesta, 'type' => $type, 'title'=> $title ];
            }     
        }

    }

