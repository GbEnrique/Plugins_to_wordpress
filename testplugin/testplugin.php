<?php 
/**
 * Plugin name: test plugin
 * Description: Un plugin de prueba
 * Version: 1.0
 * Author: Endersson García <endersson@gmail.com>
 */


 //requires

 require_once dirname(__FILE__) . '/classes/ShortCode.class.php';

function Activar(){
    
    global $wpdb;
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas (
        `EncuestaId` INT NOT NULL AUTO_INCREMENT,
        `Nombre` VARCHAR(45) NULL,
        `Shortcode` VARCHAR(45) NULL,
        PRIMARY KEY (`EncuestaId`)
    )";
    $wpdb->query($sql);
    $sql2 ="CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_detalle(
        DetalleId INT NOT NULL AUTO_INCREMENT,
        EncuestaId INT NULL,
        Pregunta VARCHAR(150) NULL,
        Tipo VARCHAR(45) NULL,
        PRIMARY KEY (DetalleId)
    )";
    $wpdb->query($sql2);

    $sql3 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_respuesta(
        RespuestaId INT NOT NULL AUTO_INCREMENT,
        DetalleId INT NULL,
        Codigo VARCHAR(45) NULL,
        Respuesta VARCHAR(45) NULL,
        PRIMARY KEY (RespuestaId )
    )";
    $wpdb->query($sql3);


}

function Desactivar(){
    flush_rewrite_rules();
}

function CrearMenu(){
    add_menu_page(
        'Super Encuestas',//Page title
        'Super Encuestas Menu',// menu title
        'manage_options',// Capability
        plugin_dir_path(__FILE__).'admin/listas_encuestas.php',//slug
        null,//function about content
        plugin_dir_url(__FILE__).'admin/img/icon.png',//direction of icon
        '1',//position of menu

    );
}

function MostrarContenido(){
    echo "<h1>contenido de la pagina</h1>";
}

//encolar bootstrap
function EncolarBootstrapJS($hook){
    // echo "<script type>console.log('$hook')</script>";
    if($hook != 'testplugin/admin/listas_encuestas.php') return;
    wp_enqueue_script('bootstrapJS',plugins_url('admin/bootstrap/js/bootstrap.min.js', __FILE__),array('jquery'));
}

function EncolarBootstrapCSS($hook){
    
    if($hook != 'testplugin/admin/listas_encuestas.php') return;
    wp_enqueue_style('bootstrapCSS',plugins_url('admin/bootstrap/css/bootstrap.min.css', __FILE__));
}

//encolar JS Propio
function EncolarJS($hook) {
    if($hook != 'testplugin/admin/listas_encuestas.php') return;
    wp_enqueue_script( 'JsExterno', plugins_url('admin/js/listas_encuestas.js', __FILE__ ), array( 'jquery' ));
    wp_localize_script('JsExterno','SolicitudesAjax',[
        'url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('seg')
    ]);
}

//Funcion Ajax
function EliminarEncuestas(){
    $nonce = $_POST['nonce'];
    if(!wp_verify_nonce($nonce,'seg')){
        die('Error no tienes permisos para ejeuctar ese ajax');
    }
    $id = $_POST['id'];
    global $wpdb;
    
    $table = "{$wpdb->prefix}encuestas";
    $table2 = "{$wpdb->prefix}encuestas_detalle";
    $wpdb->delete($table,array('EncuestaId'=>$id));
    $wpdb->delete($table2,array('EncuestaId'=>$id));
    
    return true;
}

//Funcion ShortCode 

function ImprimirShorcode ($atts){
    $_short = new ShortCode();
    //Obtener el id por parametro
    $id = $atts['id'];
    //programar las acciones del botón
    if(isset($_POST['btnguardar'])){
        $questsList = $_short->ObtenerEncuestaDetalle($id);
        $codigo = uniqid();
        foreach ($questsList as $key => $value) {
             $questId = $value['DetalleId'];
             if(isset($_POST[$questId])){
                $txtValue = $_POST[$questId];
                $data = [
                    'DetalleId' => $questId,
                    'Codigo' => $codigo,
                    'Respuesta' => $txtValue
                ];
                $_short->SaveData($data);
             }
        }

        return ' Encuesta Enviada exitosamente';
    }
    //Imprimir el formulario
    $html = $_short->Armador($id);
    
    return $html;

}

register_activation_hook(__FILE__,'Activar');
register_deactivation_hook(__FILE__,'Desactivar');

add_action('admin_menu','CrearMenu');
add_action('admin_enqueue_scripts','EncolarBootstrapJS');
add_action('admin_enqueue_scripts','EncolarBootstrapCSS');
add_action('admin_enqueue_scripts', 'EncolarJS' );
add_action('wp_ajax_deleteRequest', 'EliminarEncuestas');

add_shortcode('ENC','ImprimirShorcode');