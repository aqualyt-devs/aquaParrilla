<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Aqualyt - Parrilla</title>
    <?php include 'styles.php';?>
</head>

<body>
<?php 
    include 'urlAPI.php';
    include 'checkToken.php';

    if ((!isset($_COOKIE["AquaToken"])) || (checkToken($urlAPI)->codigo==200)) {
        header("Refresh:0; url=index.php");
    } else {
        include 'functions.php';
        if (isset($_GET['fecha'])) {
            $fecha = $_GET['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
    ?>
    <div class="navbar-fixed">
        <nav class="blue darken-3" role="navigation">
            <div class="nav-wrapper">
                <a id="logo-container" href="index.php" class="brand-logo">
                    <img src="imgs/logo_negativo.png">
                </a>
                <a href="#" data-activates="mobileMenu" class="button-collapse"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href="#" class="datepicker" data-value="" id="selectorFecha">
                            <i class="material-icons left">date_range</i>
                            <span id="filtroFecha"></span>
                        </a>
                    </li>
                    <li>
                        <a href="../aquaoperario">
                            <i class="material-icons left">account_circle</i>Operario</a>
                    </li>
                    <li>
                        <a href="contactoControl.php">
                            <i class="material-icons left">people</i>Contactos</a>
                    </li>
                    <li>
                        <a href="index.php">
                            <i class="material-icons left">list</i>Control PS</a>
                    </li>
                    <li>
                        <a href="#modalPausa" class="modal-trigger">
                            <i class="material-icons left">pause</i>Asignar pausa</a>
                    </li>
                    <li>
                        <a href="index.php?logout=1">
                            <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
                    </li>
                </ul>
                <ul class="side-nav" id="mobileMenu">
                    <li>
                        <a href="#" class="datepicker" data-value="" id="selectorFecha">
                            <i class="material-icons left">date_range</i>
                            <span id="filtroFechaMobile"></span>
                        </a>
                    </li>
                    <li>
                        <a href="../aquaoperario">
                            <i class="material-icons left">account_circle</i>Operario</a>
                    </li>
                    <li>
                        <a href="contactoControl.php">
                            <i class="material-icons left">people</i>Contactos</a>
                    </li>
                    <li>
                        <a href="index.php">
                            <i class="material-icons left">list</i>Control PS</a>
                    </li>
                    <li>
                        <a href="#modalPausa" class="modal-trigger">
                            <i class="material-icons left">pause</i>Asignar pausa</a>
                    </li>
                    <li>
                        <a href="index.php?logout=1">
                            <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <main>
        <div class="section">
            <div class="row">
                <div class="col s5" id="mapaBig">
                    <div id="map" class="mapa z-depth-2"></div>
                </div>
                <div class="col s7">
                    <ul class="collection z-depth-2 optiscroll columnHeight" id="equiposListCol">
                    <?php 
                        $listEquipos = getContent($urlAPI."/equipos/cola_ps/equipos");
                        if ($listEquipos->codigo == 100) {
                            $modalCancelarCola = '';
                            foreach ($listEquipos->contenido as $equipo) {
                                $PSActual = 'No hay PS Asignada';
                                $PSSiguiente = 'No hay PS en la cola';
                                $PSAnterior = 'No hay PS anterior';
                                $PSTT = '';

                                if (($equipo->EQUIPO->lat == 'None') || ($equipo->EQUIPO->direccion_gps == "None")) {
                                    $equipo->EQUIPO->direccion_gps = "Carrer de Caracas 13, 08030, Barcelona";
                                }
                
                                if ($equipo->PS->tiempo_teorico != '00:00:00') {
                                    $PSTT = '<br />T.T.: ' . $equipo->PS->tiempo_teorico . ' h.';
                                }

                                foreach ($equipo->COLA as $keyPS => $valPSCOLA) {
                                    if ($valPSCOLA->agrupada) {
                                        $agrupada = 1;
                                    } else {
                                        $agrupada = 0;
                                    }

                                    $desasignarBtn = '<br /><a class="modal-trigger red-text text-darken-2" href="#modalCancelar'.$valPSCOLA->idPS.'">Cancelar cola</a>';
                                    
                                    if ($keyPS == 0) {
                                        $PSActual = iraPSAquaplus($valPSCOLA->idPS) . $PSTT . $desasignarBtn;
                                    } else {
                                        $PSSiguiente = iraPSAquaplus($valPSCOLA->idPS) . $desasignarBtn;
                                    }
                                    $modalCancelarCola .= '<div id="modalCancelar'. $valPSCOLA->idPS .'" class="modal">
                                                                <div class="modal-content">
                                                                    <h4>Cancelar PS '. $valPSCOLA->idPS .' asignada al Equipo '.$equipo->NOMBRE.'
                                                                    </h4>
                                                                    <p>¿Quieres cancelar la PS '.$valPSCOLA->idPS.' asignada al Equipo '.$equipo->NOMBRE.'?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
                                                                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn red desasignarColaPSEquipo" idPS="'.$valPSCOLA->idPS.'" idEquipo="'.$valPSCOLA->idEquipo.'" agrupada="'.$agrupada.'">Cancelar</a>
                                                                </div>
                                                            </div>';
                                }

                                echo '<li class="collection-item avatar estadoEquipo' . $equipo->EQUIPO->IDEstado . '"><i class="z-depth-2 material-icons circle grey">place</i><div class="row"><div class="col s6"><span class="title">' . $equipo->NOMBRE . '</span><p>' . $equipo->EQUIPO->direccion_gps . '</p></div><div class="col s2">PS Anterior:<br />' . iraPSAquaplus($equipo->ULTIMO_SERVICIO_REALIZADO) . '</div><div class="col s2">PS Actual:<br />' . $PSActual . '</div><div class="col s2">PS en cola:<br />' . $PSSiguiente . '</div></div></li>';
                            }
                        } else {
                            echo '<li class="collection-item avatar estadoEquipo0"><i class="z-depth-2 material-icons circle red">warning</i><div class="row"><div class="col s6"><span class="title">No hay equipos</span><p></p></div></li>';
                        }

                        $listEquiposSinPS = getContent($urlAPI."/equipos/alertas?idTipoAlerta=7&date=".$fecha);
                        if ($listEquiposSinPS->codigo == 100) {
                            $plural = '';
                            if (count($listEquiposSinPS->contenido)>1) {
                                $plural = 's';
                            }
                            echo '<li class="collection-item estadoEquipo38">Equipo' . $plural . ' sin PS asignada</li>';
                            foreach ($listEquiposSinPS->contenido as $equipoSinPS) {
                                echo '<li class="collection-item avatar estadoEquipo38"><i class="z-depth-2 material-icons circle grey">place</i><div class="row"><div class="col s6"><span class="title">' . $equipoSinPS->nomEquipo . '</span></div><div class="col s3">Sin PS asignada</div><div class="col s3"><a class="red-text text-darken-2 cleanAlert" href="#" idAlerta="'. $equipoSinPS->id .'">Limpiar aviso</a></div></div></li>';
                            }
                        }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
        <br>
        <br>
    </main>
    <!-- Modals -->
    <?php 
        echo $modalCancelarCola;
    ?>
    <div id="modalCancelar" class="modal">
        <div class="modal-content">
            <h4>Cancelar PS
                <span class="idPSCancelar"></span> asignado al Equipo
                <span class="idEquipoCancelar"></span>
            </h4>
            <p>¿Quieres cancelar el PS
                <span class="idPSCancelar"></span> asignado al Equipo
                <span class="idEquipoCancelar"></span>?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="desasignarPSaEquipo">Cancelar</a>
        </div>
    </div>
    <div id="modalPausa" class="modal">
        <div class="modal-content">
            <h4>Asignar pausa</h4>
            <div class="row">
                <form action="#" class="col s12" id="pausaForm">
                    <div class="col s3 input-field">
                        <input type="text" class="datepickerPausa" placeholder="Fecha" id="datePickerPausa">
                        <label for="datePickerPausa">Fecha</label>
                    </div>
                    <div class="col s3 input-field">
                        <input type="text" class="timepicker" placeholder="Hora" id="timePickerPausa">
                        <label for="timePickerPausa">Hora</label>
                    </div>
                    <div class="col s3 input-field">
                        <select id="equiposSelect">
                            <option value="" disabled selected>Selecciona un equipo:</option>
                        </select>
                    </div>
                    <div class="" style="margin-top: 0px;">
                        <p>
                            <input name="tipoComida" type="radio" id="bocata" value="bocata" checked />
                            <label for="bocata">Bocata</label>
                        </p>
                        <p>
                            <input name="tipoComida" type="radio" id="comida" value="comida" />
                            <label for="comida">Comida</label>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="pausarBtn">Asignar</a>
        </div>
    </div>
<?php
include 'scripts.php';
}?>
</body>

</html>