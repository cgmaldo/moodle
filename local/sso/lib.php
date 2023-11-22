<?php 

function paginaCustom($tituloVentana, $tituloBody, $mensaje, $tipoMensaje) {
    global $OUTPUT, $PAGE;

    $PAGE->set_url('/local/sso/sso.php');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title($tituloVentana);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($tituloBody);
    echo $OUTPUT->notification($mensaje, 'notify{$tipoMensaje}');
    echo $OUTPUT->footer();
    exit;
    die(); 
}

