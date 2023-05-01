<?php
    class ShortCode{
        public function ObtenerEncuesta($encuestaid){
            global $wpdb;
            $tabla = "{$wpdb->prefix}encuestas";
            $query="SELECT * FROM $tabla WHERE EncuestaId= '$encuestaid'";
            $datos = $wpdb->get_results($query, ARRAY_A);
            if(empty ($datos)) {
                $datos = array();
            }
            return $datos[0];
        }
        public function ObtenerEncuestaDetalle($encuestaid){
            global $wpdb;
            $tabla = "{$wpdb->prefix}encuestas_detalle";
            $query="SELECT * FROM $tabla WHERE EncuestaId = '$encuestaid'";
            $datos = $wpdb->get_results($query,ARRAY_A);
            if(empty($datos)){
                $datos = array();
            }
            return $datos;
        }

        public function formOpen($titulo){
            $html = "
            <div class='wrap'>
                <h4> $titulo</h>
                <br>
                <form method= 'POST'>";
            return $html ;
        }
        public function formClose(){
            $html ="
                    <br>
                    <input type='submit' id='btnguardar' name='btnguardar' class='page-title-action' value='enviar' >
                </form>
            </div>";
            ;
            return $html;
        }
        public function fromInput($detalleId,$pregunta,$tipo){
            $html = "";
            if ($tipo==1) {
                $html = "
                    <div class='from-group'>
                        <p><b>$pregunta</b></p>
                        <div class='col-sm-8'>
                            <select class='from-control' id='$detalleId' name='$detalleId'>
                                <option value='SI'>SI</option>
                                <option value='No'>NO</option>
                            </select>
                    </div>
                ";
            }elseif ($tipo==2) {

            }else{

            }

            return $html;


        }

        public function Armador($encuestaid){
            $enc = $this->ObtenerEncuesta($encuestaid);
            $nombre = $enc['Nombre'];
            //optener todas las preguntas
            $quests = "";
            $questsList = $this->ObtenerEncuestaDetalle($encuestaid);
            foreach ($questsList as $key => $value) {
                $detalleId = $value['DetalleId'];
                $pregunta = $value['Pregunta'];
                $tipo = $value['Tipo'];
                $encid = $value['EncuestaId'];

                if($encid == $encuestaid){
                    $quests .= $this->fromInput($detalleId,$pregunta,$tipo);
                }
            }
            $html = $this->formOpen($nombre);
            $html .= $quests;
            $html .= $this->formClose();

            return $html;

        }

        public function SaveData($data){
            global $wpdb;
            $tabla = "{$wpdb->prefix}encuestas_respuesta";
            return $respuesta = $wpdb->insert($tabla, $data);
        }
    }

    ?>