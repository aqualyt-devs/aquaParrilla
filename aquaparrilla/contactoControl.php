<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Aqualyt - Parrilla</title>
    <?php include 'styles.php'; ?>
</head>

<body>
    <?php
    include 'urlAPI.php';
    include 'checkToken.php';

    if ((!isset($_COOKIE["AquaToken"])) || (checkToken($urlAPI)->codigo == 200)) {
        header("Refresh:0; url=index.php");
    } else {
        include 'functions.php';
        $idPS = null;
        $idContacto = null;

        if (isset($_GET['idPS'])) {
            $idPS = $_GET['idPS'];
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
                            <a href="controlEquipos.php" class="controlEquiposMenu">
                                <i class="material-icons left">directions_car</i>Control equipos </a>
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
                    <?php
                    $listContactos = getContent($urlAPI . "/equipos/contactos/temporal/get");
                    $arrayContactos = array();
                    if ($listContactos->codigo == 100) {
                        foreach ($listContactos->contenido as $contacto) {
                            if (!isset($arrayContactos[$contacto->ps])) {
                                $arrayContactos[$contacto->ps] = array();
                            }
                            $arrayContactos[$contacto->ps][$contacto->id] = $contacto;
                        }
                    }

                    ksort($arrayContactos);

                    echo '<div class="col s2"><div id="PSListContactos" class="collection z-depth-2 optiscroll columnHeight">
                            <div class="collection-item input-field">
                                <input id="buscadorPS" placeholder="Buscar PS" type="text">
                            </div>';
                    foreach ($arrayContactos as $key => $value) {
                        echo "<a href='contactoControl.php?idPS=$key' class='collection-item";
                        if ($idPS == $key) {
                            echo ' active';
                        }
                        echo "' idPS='$key'>PS $key</a>";
                    }
                    echo '</div></div>';

                    echo '<div class="col s10"><div class="collection z-depth-2 optiscroll columnHeight">';
                    echo ($idPS !== null) ? "<div class='col s12'><h5><a href='http://app.aqualyt.net/sf/action.php?SOURCE=PS2&amp;ACTION=SHOWINITIAL&amp;LOAD_PS=$idPS' target='_blank'>PS $idPS</a></h5></div>" : "";
                    if ($idPS !== null) {
                        if (!array_key_exists($idPS, $arrayContactos)) {
                            echo "<div class='col s12'><h4>No hay contactos temporales para la PS $idPS</h4></div>";
                        } else {
                            echo '<table class="striped centered">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>Teléfono</th>
                                            <th>Móvil</th>
                                            <th>Email</th>
                                            <th>Dirección</th>
                                            <th>Observaciones</th>
                                            <th>Creado por</th>
                                            <th>fecha</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                            
                                    <tbody>';
                            foreach ($arrayContactos[$idPS] as $key => $value) {
                                $address = array($value->calle, $value->numero_calle, $value->piso, $value->cp, $value->poblacion, $value->provincia);
                                echo "<tr class='clickable-row' data-href='url://'>
                                        <td>$value->nombre</td>
                                        <td>$value->apellido1 $value->apellido2</td>
                                        <td>$value->telf</td>
                                        <td>$value->movil</td>
                                        <td>$value->email</td>
                                        <td>" . join(', ', array_filter($address)) . "</td>
                                        <td>$value->observaciones</td>
                                        <td>$value->nombreCreador</td>
                                        <td>$value->fecha</td>
                                        <td><a href='contactoComprobar.php?idPS=$idPS&idContacto=$value->id' class='modal-trigger btn-flat actionBtn mantenimientoInstalacion'><i class='material-icons'>assignment_turned_in</i></a></td>
                                    </tr>";
                            }
                            echo '</tbody>
                                    </table>';
                        }
                    } else {
                        echo "<div class='col s12'><h4>Selecciona una PS del menú lateral</h4></div>";
                    }
                    echo '</div></div>';
                    ?>
                </div>
            </div>
            <br>
            <br>
        </main>
        <!-- Modals -->
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
    } ?>
</body>

</html>