<?php
    global $wpdb;

    $table = "{$wpdb->prefix}encuestas";
    $table2 = "{$wpdb->prefix}encuestas_detalle";

    if(isset($_POST['btnguardar'])){
        $name = $_POST['nametxt'];
        $query = "SELECT * FROM $table ORDER BY EncuestaId DESC limit 1";
        $resultado =$wpdb->get_results($query, OBJECT);
        $proximoId = $resultado[0]->EncuestaId + 1;
        $shortcode = "[ENC id='$proximoId']";

        $data = [
            'EncuestaId' =>null,
            'Nombre' => $name,
            'Shortcode' => $shortcode

        ];
        $response = $wpdb->insert($table, $data);
        $proximoId = mysqli_insert_id($wpdb->dbh);
        if($response){
            $question_list = $_POST['name'];
            foreach ($question_list as $key => $value) {
                $type = $_POST['type'][$key];
                $data2 = [
                    'DetalleId' => null,
                    'Encuestald' => $proximoId,
                    'Pregunta' =>$value,
                    'Tipo' => $type
                ];
                $wpdb->insert($table2, $data2);
                
            }
        }
    }

    $sql = "SELECT * FROM $table";    
    $lista_encuestas = $wpdb->get_results($sql,OBJECT);
?>


<div class="wrap">
    <?php
        echo "<h1 class=wp-heading-inline'>". get_admin_page_title(). "</ht>";
    ?>
    <a id="btnnuevo" class="page-title-action"> Anadir nuevo</a>
    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <th>Nombre de la encuesta</th>
            <th >shortcode</th>
            <th >Accicnes</th>
        </thead>
        <tbody id="the-list">
            <?php 
                foreach ($lista_encuestas as $key => $value) { 
                    
                    echo "<tr>
                            <td>$value->Nombre</td>
                            <td>$value->Shortcode</td>
                            <td>
                                <a class='page-title-action'>Ver estadisticas</a>
                                <a data-id = ". $value->EncuestaId ." class='page-title-action'>Borrar</a>
                            </td>
                        </tr>";
                }                
                
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nueva Encuesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="post">
        <div class="modal-body">

            <div class="row">
                <label for="nametxt" class="col-sm-5 form-label">Nombre de la encuesta</label>
                <div class="col-sm">
                    <input type="text" class="form-control" id="nametxt" name="nametxt">
                </div>
            </div> 
            <hr>
            <h4> Preguntas</h4>
            <br>
            <button type="button" name="add" id="add" class="btn btn-success mb-2">Agregar nuevo</button>
            <table id="camposdinamicos">
                <tr class="">
                    <td>
                        <label for="name" class="form-label">Pregunta 1</label>
                    </td>
                    <td>
                        <input type="text" name="name[]" id="name" class="form-control name_list">
                    </td>
                    <td>
                        <select name="type[]" id="type" class="form-control type_list">
                            <option value="1" selected>SI - NO</option>
                            <option value="2">Rango 0 - 5</option>
                            <option value="3">Respuesta Breve</option>
                        </select>
                    </td>
                                       
                </tr>
            </table>           
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="" class="btn btn-primary" id="btnguardar" name="btnguardar">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>