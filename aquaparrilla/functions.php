<?php

function getContent($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "token: " . $_COOKIE['AquaToken']
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response);
}

function postContent($url, $arrayData)
{
    $data_string = json_encode($arrayData);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ),
        'cache-control: no-cache',
        'token: ' . $_COOKIE['AquaToken']
    );

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response);
}

function iraPSAquaplus($idPS)
{
    return "<b><a href='http://app.aqualyt.net/sf/action.php?SOURCE=PS2&ACTION=SHOWINITIAL&LOAD_PS=" . $idPS . "' target='_blank'>PS " . $idPS . "</a></b>";
}

function iraPersonaAquaplus($idPersona)
{
    return '<form id="form_go_personas2" action="http://app.aqualyt.net/sf/action.php" method="POST" style="display:inline;" target="_blank">
                <input id="go_personas2" name="go_personas2" type="submit" value="' . $idPersona . '" title="IR a la persona">
                <input type="hidden" id="ACTION" name="ACTION" value="EDIT_EXTERNAL">
                <input type="hidden" id="SOURCE" name="SOURCE" value="PERSONAS2">
                <input type="hidden" id="idPersona" name="idPersona" value="' . $idPersona . '">
            </form>';
}

function codeOut($string)
{
    echo '<pre>';
    print_r($string);
    echo '</pre>';
}

function trComparaContactos($comparativaContacto, $campo)
{
    $campo0 = '';
    $campo1 = '';

    if (isset($comparativaContacto[0]->$campo)) {
        $campo0 = '<p><input name="' . $campo . '" type="radio" id="' . $campo . '0" value="' . $comparativaContacto[0]->$campo . '" /><label for="' . $campo . '0">' . $comparativaContacto[0]->$campo . '</label></p>';
    }
    if (isset($comparativaContacto[1]->$campo)) {
        $campo1 = '<p><input name="' . $campo . '" type="radio" id="' . $campo . '1" value="' . $comparativaContacto[1]->$campo . '" /><label for="' . $campo . '1">' . $comparativaContacto[1]->$campo . '</label></p>';
    }

    return '<tr>
                <td>' . trNombre($campo) . '</td>
                <td>' . $campo0 . '</td>
                <td>' . $campo1 . '</td>
            </tr>';
}

function trContactos($comparativaContacto, $campo)
{
    return '<tr>
                <td>' . trNombre($campo) . '</td>
                <td>' . ((isset($comparativaContacto[0]->$campo)) ? $comparativaContacto[0]->$campo : '') . '</td>
                <td>' . ((isset($comparativaContacto[1]->$campo)) ? $comparativaContacto[1]->$campo : '') . '</td>
            </tr>';
}

function trSelectContactosCreador($comparativaContacto)
{
    $arrayUsuarios = [];
    $creador = 0;
    array_push($arrayUsuarios, array('id' => $_COOKIE['AquaTokenID'], 'nombre' => str_replace('+', ' ', $_COOKIE['AquaTokenNOMBRE']), 'seleccionado' => false));

    foreach ($comparativaContacto as $contacto) {
        if (($contacto->creado_por !== null) && ($contacto->creado_por !== '0')) {
            array_push($arrayUsuarios, array('id' => $contacto->creado_por, 'nombre' => $contacto->nombreCreador . (isset($contacto->fecha_creacion) ? ' (Creado el ' . date_format(date_create($contacto->fecha_creacion, timezone_open('Europe/Madrid')), 'd/m/Y H:i:s') . ')' : ''), 'seleccionado' => ((($creador == 0) && (isset($contacto->fecha_creacion))) ? true : false)));
            if (($creador == 0) && (isset($contacto->fecha_creacion))) {
                $creador++;
            }
        }
        if ((isset($contacto->modificado_por)) && ($contacto->modificado_por !== null) && ($contacto->modificado_por !== '')) {
            array_push($arrayUsuarios, array('id' => $contacto->modificado_por, 'nombre' => $contacto->nombreModificador, 'seleccionado' => false));
        }
    }

    $select = '<tr>
                <td>Creado por</td>
                <td colspan="2">
                    <div class="input-field col s12">
                        <select id="creado_por" name="creado_por">
                        <option value="" disabled>Selecciona una opción</option>
                        <option value="">Sin creador</option>';
    foreach ($arrayUsuarios as $usuario) {
        $select .= '<option value="' . $usuario['id'] . '" ' . ($usuario['seleccionado'] ? "selected" : "") . '>' . $usuario['nombre'] . '</option>';
    }
    $select .= '</select>
                        <label>Selecciona creador</label>
                    </div>
                </td>
            </tr>';

    return $select;
}

function trSelectFechasCreacion($comparativaContacto)
{
    $arrayFechas = [];
    $ahora = new DateTime("now", new DateTimeZone('Europe/Madrid'));
    $ahora = $ahora->format('Y-m-d H:i:s');

    array_push($arrayFechas, array('fecha' => $ahora, 'seleccionado' => false));

    foreach ($comparativaContacto as $contacto) {
        if ((isset($contacto->fecha_creacion)) && ($contacto->fecha_creacion !== null) && ($contacto->fecha_creacion !== '') && ($contacto->fecha_creacion != 0)) {
            array_push($arrayFechas, array('fecha' => $contacto->fecha_creacion, 'seleccionado' => (count($arrayFechas) == 1 ? true : false)));
        }
    }

    $select = '<tr>
                <td>Fecha creación</td>
                <td colspan="2">
                    <div class="input-field col s12">
                        <select id="fecha_creacion" name="fecha_creacion">
                        <option value="" disabled>Selecciona una opción</option>
                        <option value="">Sin fecha</option>';
    foreach ($arrayFechas as $fecha) {
        $select .= '<option value="' . $fecha['fecha'] . '" ' . ($fecha['seleccionado'] ? "selected" : "") . '>' . date_format(date_create($fecha['fecha'], timezone_open('Europe/Madrid')), 'd/m/Y H:i:s') . '</option>';
    }
    $select .= '</select>
                        <label>Fecha creación</label>
                    </div>
                </td>
            </tr>';

    return $select;
}

function trSelectContactosModificador($comparativaContacto)
{
    $arrayUsuarios = [];

    array_push($arrayUsuarios, array('id' => $_COOKIE['AquaTokenID'], 'nombre' => str_replace('+', ' ', $_COOKIE['AquaTokenNOMBRE']), 'seleccionado' => true));

    foreach ($comparativaContacto as $contacto) {
        if ((isset($contacto->modificado_por)) && ($contacto->modificado_por !== null) && ($contacto->modificado_por !== '')) {
            array_push($arrayUsuarios, array('id' => $contacto->modificado_por, 'nombre' => $contacto->nombreModificador . ' (Modificado el ' . date_format(date_create($contacto->fecha_modificado, timezone_open('Europe/Madrid')), 'd/m/Y H:i:s') . ')', 'seleccionado' => false));
        }
        if (($contacto->creado_por !== null) && ($contacto->creado_por !== '0')) {
            array_push($arrayUsuarios, array('id' => $contacto->creado_por, 'nombre' => $contacto->nombreCreador, 'seleccionado' => false));
        }
    }

    $select = '<tr>
                <td>Modificado por</td>
                <td colspan="2">
                    <div class="input-field col s12">
                        <select id="modificado_por" name="modificado_por">
                        <option value="" disabled>Selecciona una opción</option>
                        <option value="">Sin modificación</option>';
    foreach ($arrayUsuarios as $usuario) {
        $select .= '<option value="' . $usuario['id'] . '" ' . ($usuario['seleccionado'] ? "selected" : "") . '>' . $usuario['nombre'] . '</option>';
    }
    $select .= '</select>
                        <label>Selecciona modificador</label>
                    </div>
                </td>
            </tr>';

    return $select;
}

function trSelectFechasModificacion($comparativaContacto)
{
    $arrayFechas = [];
    $ahora = new DateTime("now", new DateTimeZone('Europe/Madrid'));
    $ahora = $ahora->format('Y-m-d H:i:s');

    array_push($arrayFechas, array('fecha' => $ahora, 'seleccionado' => true));

    foreach ($comparativaContacto as $contacto) {
        if ((isset($contacto->fecha_modificado)) && ($contacto->fecha_modificado !== null) && ($contacto->fecha_modificado !== '') && ($contacto->fecha_modificado != 0)) {
            array_push($arrayFechas, array('fecha' => $contacto->fecha_modificado, 'seleccionado' => false));
        }
    }

    $select = '<tr>
                <td>Fecha modificación</td>
                <td colspan="2">
                    <div class="input-field col s12">
                        <select id="fecha_modificado" name="fecha_modificado">
                        <option value="" disabled>Selecciona una opción</option>
                        <option value="">Sin fecha</option>';
    foreach ($arrayFechas as $fecha) {
        $select .= '<option value="' . $fecha['fecha'] . '" ' . ($fecha['seleccionado'] ? "selected" : "") . '>' . date_format(date_create($fecha['fecha'], timezone_open('Europe/Madrid')), 'd/m/Y H:i:s') . '</option>';
    }
    $select .= '</select>
                        <label>Fecha modificación</label>
                    </div>
                </td>
            </tr>';

    return $select;
}

function trNombre($campo)
{
    $arrayCampo = array('id', 'nombre', 'apellido1', 'apellido2', 'telef1', 'telef2', 'movil', 'email', 'tipo_calle', 'calle', 'numero_calle', 'piso', 'cp', 'observaciones', 'dni', 'poblacion', 'provincia', 'nombreCreador', 'fecha_modificado', 'creado_por');
    $arrayNombre = array('ID', 'Nombre', 'Apellido 1', 'Apellido 2', 'Teléfono 1', 'Teléfono 2', 'Móvil', 'email',  'Tipo calle', 'Calle', 'Número', 'Piso', 'CP', 'Observaciones', 'DNI', 'Población', 'Provincia', 'Creador', 'Fecha Modificado', 'creado_por');

    $array = array_combine($arrayCampo, $arrayNombre);

    return $array[$campo];
}

function makeSelect($items, $label, $id, $selected)
{
    $select = '<select id="' . $id . '" name="' . $id . '"><option value="" ' . ((array_key_exists($selected, $items) && $selected != 0) ? '' : 'selected') . '>Selecciona ' . strtolower($label) . '</option>';
    foreach ($items as $id => $value) {
        $select .= '<option value="' . $id . '" ' . (($selected == $id) ? 'selected' : '') . '>' . $value . '</option>';
    }
    $select .= '</select><label>' . $label . '</label>';
    return $select;
}

function objectToArray($obj)
{
    $arr = array();
    foreach ($obj as $v) {
        if (property_exists($v, 'nombre')) {
            $arr[$v->id] = $v->nombre;
        } elseif (property_exists($v, 'tipo')) {
            $arr[$v->id] = $v->tipo;
        } else {
            $arr[$v->id] = $v->direccion;
        }
    }
    asort($arr);
    return $arr;
}
