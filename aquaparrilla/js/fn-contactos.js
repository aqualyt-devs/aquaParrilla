$(() => {
    const Token = Cookies.get('AquaToken');
    const idUsuario = Cookies.get('AquaTokenID');
    const personasComparar = [];
    let contactoComparar = null;
    const $idPS = $('#idPS').val();
    const $idContactoTemporal = $('#idContactoTemporal').val();
    let $selectContactoTemporal = $('#selectContactoTemporal');
    let $selectContacto = $('.selectContacto');
    let $compararContactosBtn = $('#compararContactos');
    let $unirContactosBtn = $('.unirContactos');
    let $unirPersonaContactoBtn = $('#unirPersonaContacto');
    let $updateContactoBtn = $('#updateContacto');
    let $saveContactoBtn = $('#saveContacto');
    let $deleteContactoBtn = $('#deleteContacto');
    let $comparativaContactos = $('#comparativaContactos');
    let $infoContacto = $('#infoContacto');
    let $desvincularContactosBtn = $('#desvincularContactos');
    let $copiarDireccionBtn = $('#copiarDireccion');
    let $unirModalBtn = $('.unir');
    let $listUnchecked = $('.unchecked');
    let $avisoUnchecked = $('.avisoUnchecked');
    var URLAPI = "http://api.aqualyt.net:5002";
    // var URLAPI = "http://80.28.134.219:5002";

    $('select').material_select();
    $avisoUnchecked.hide();

    $compararContactosBtn.hide();

    $.ajaxSetup({
        cache: false,
        headers: {
            'token': Token,
            "Content-Type": "application/json"
        }
    });

    $selectContactoTemporal.on('click', () => selCont($selectContactoTemporal, 1));
    $selectContacto.on('click', function() { selCont($(this), 2) });

    $unirContactosBtn.on('click', function() { unirContactos($comparativaContactos, $(this).attr('idPersona1'), $(this).attr('idPersona2')) });
    $unirPersonaContactoBtn.on('click', function() { unirPersonaContacto($comparativaContactos, $(this).attr('idPersona'), $(this).attr('idContacto')) });
    $unirModalBtn.on('click', () => checkUnion());
    $desvincularContactosBtn.on('click', () => unlinkContactos($comparativaContactos));
    $updateContactoBtn.on('click', function() { updateContacto($(this).attr('idContacto')) });
    $saveContactoBtn.on('click', function() { saveContacto($(this).attr('idContacto')) });
    $copiarDireccionBtn.on('click', function() { copiarDireccion() });
    $deleteContactoBtn.on('click', function() { deleteContacto($(this).attr('idContacto')) });

    const selCont = function seleccionContactosAComparar(contacto, tipo) {
        if (tipo === 1) {
            // Contacto temporal
            mCont(contacto);
        } else {
            // Persona sistema
            mPers(contacto);
        }

        canCompare();
    };

    const mCont = function marcarContactoAComparar(contacto) {
        let idContacto = contacto.attr('idContacto');
        if (contactoComparar == idContacto) {
            contactoComparar = null;
            unselect(contacto);
        } else {
            contactoComparar = idContacto;
            select(contacto);
        }
    }

    const mPers = function marcarPersonaAComparar(contacto) {
        let idContacto = contacto.attr('idContacto');
        let arrayIndex = $.inArray(idContacto, personasComparar);

        if (arrayIndex !== -1) {
            personasComparar.splice(arrayIndex, 1);
            unselect(contacto);
        } else {
            personasComparar.push(idContacto);
            select(contacto);
        }
    }

    const select = function setCheckboxSelected(contacto) {
        contacto.find('i').html('check_box')
        contacto.addClass('green-text text-darken-2');
    }

    const unselect = function setCheckboxNotSelected(contacto) {
        contacto.find('i').html('check_box_outline_blank')
        contacto.removeClass('green-text text-darken-2');
    }

    const canCompare = function puedenCompararse() {
        let lengthPersonas = personasComparar.length;
        if (((contactoComparar !== null) && (lengthPersonas == 1)) || (lengthPersonas == 2)) {
            disSelects();
        } else {
            enaSelects();
        }
    }

    const disSelects = function disableSelectorContactos() {
        $selectContacto.addClass('disabled');
        personasComparar.map((persona) => $("a[idContacto='" + persona + "']").removeClass('disabled'))
        if (contactoComparar === null) {
            $selectContactoTemporal.addClass('disabled');
        }
        actCompBtn();
    }

    const enaSelects = function enableSelectorContactos() {
        // let numPersonas = $('.selectContacto').length;
        $compararContactosBtn.hide();
        $selectContacto.removeClass('disabled');
        // (numPersonas < 2) ? $selectContactoTemporal.removeClass('disabled'): '';
        $selectContactoTemporal.removeClass('disabled');
    }

    const actCompBtn = function activarBotonCompararContactos() {
        let stringPersonas = personasComparar.join();

        $compararContactosBtn.attr('href', `contactoComparar.php?idPS=${$idPS}&idCT=${$idContactoTemporal}&idContacto=${contactoComparar}&idPersona=${stringPersonas}`);
        $compararContactosBtn.show();
    }

    const updateContacto = function actualizarDatosContactosFormulario(idContacto) {
        let data = JSONForm($infoContacto);
        let url = URLAPI + '/equipos/contactos/edit/temporal/' + idContacto;

        putData(url, data)
            .then((response) => {
                if (response.codigo == 100) {
                    Materialize.toast('Datos de contacto actualizados', 4000);
                }
            })
    }

    const saveContacto = function generaPersonaContactosFormulario(idContactoTemporal) {
        let data = JSONForm($infoContacto);
        let url = URLAPI + '/equipos/contactos/edit/temporal/' + idContactoTemporal;

        putData(url, data)
            .then((responsePut) => {
                if (responsePut.codigo == 100) {
                    $.post(URLAPI + '/equipos/crear_persona_from_temporal/' + idContactoTemporal + '/' + idUsuario, {}, function(responsePost) {
                        if (responsePost.codigo == 200) {
                            Materialize.toast(responsePost.detalles, 4000);
                        } else if (responsePost.codigo == 100) {
                            Materialize.toast('Contacto guardado en el sistema correctamente', 2000, '', function() { window.open("./contactoControl.php?idPS=" + $idPS, '_self'); });
                        }
                    }, 'json');
                } else {
                    Materialize.toast(responsePut.detalles, 4000);
                }
            })

    }

    const copiarDireccion = function copiarDireccionDeSistema() {
        console.log('HOLA');

        jQuery.getJSON(URLAPI + "/equipos/direccion/ps/" + $idPS, function(json) {
            let direccion = json.contenido[1];
            $('#calle').val(direccion.calle);
            $('#numero_calle').val(direccion.num_calle);
            $('#piso').val(direccion.piso);
            $('#cp').val(direccion.cp);
            $('#poblacion').val(direccion.poblacion);
            $('#provincia').val(direccion.provincia);
            $('#tipo_calle').val(direccion.tipo_calle);
            $('#tipo_calle').material_select();
            Materialize.updateTextFields();
        });
    }

    const updatePersona = function cargaDatosTemporalAPersona() {}

    const unlinkContactos = function deslincarContactos($infoContacto) {
        let $relacion = $('#relacion').val();
        let $idContacto1 = $('#idContacto1').val();
        let $idContacto2 = $('#idContacto2').val();

        if ($relacion === '1') {
            ulinkContPers($idContacto1, $idContacto2);
        } else {
            ulinkPers($idContacto1, $idContacto2);
        }
    }

    const deleteContacto = function eliminarContactoEnEspera(idContactoEspera) {
        $.post(URLAPI + '/equipos/contactos/temporal/delete/' + idContactoEspera, {}, function(response) {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
            } else if (response.codigo == 100) {
                Materialize.toast('Contacto eliminado', 2000, '', function() { window.open("./contactoControl.php?idPS=" + $idPS, '_self'); });
            }
        }, 'json');
    }

    const valContacto = function validarContactoEnEspera(idContactoEspera) {
        $.post(URLAPI + '/equipos/contactos/temporal/validate/' + idContactoEspera, {}, function(response) {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
            } else if (response.codigo == 100) {
                window.open("./contactoControl.php?idPS=" + $idPS, '_self');
            }
        }, 'json');
    }

    const ulinkContPers = function desvincularContactoYPersona(idtemporal, idPersona) {
        $.post(URLAPI + '/equipos/desmarcar_coincidencia_persona_temporal/persona/' + idPersona + '/temporal/' + idtemporal + '/idUsuario/' + idUsuario, {}, function(response) {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
            } else if (response.codigo == 100) {
                Materialize.toast('Contactos desenlazados correctamente', 2000, '', function() { window.open("./contactoComprobar.php?idPS=" + $idPS + "&idContacto=" + idtemporal, "_self"); });
            }
        }, 'json');
    }

    const ulinkPers = function desvincularPersonas(idPersona1, idPersona2) {
        console.log('url');
        console.log(URLAPI + '/equipos/desmarcar_coincidencia_persona/persona1/' + idPersona1 + '/persona2/' + idPersona2 + '/idUsuario/' + idUsuario);

        $.post(URLAPI + '/equipos/desmarcar_coincidencia_persona/persona1/' + idPersona1 + '/persona2/' + idPersona2 + '/idUsuario/' + idUsuario, {}, function(response) {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
            } else if (response.codigo == 100) {
                Materialize.toast('Contactos desenlazados correctamente', 2000, '', function() { window.open("./contactoControl.php?idPS=" + $idPS, "_self"); });
            }
        }, 'json');
    }

    const checkUnion = function checkValoresMarcadosComparativa() {
        let radioGroups = {};
        let showUnchecked = false;
        $listUnchecked.empty();

        $(":input:radio").each(function() {
            radioGroups[this.name] = true;
        });

        for (let group in radioGroups) {
            let if_checked = !!$(":radio[name='" + group + "']:checked").length;
            if (!if_checked) {
                showUnchecked = true;
                $listUnchecked.append('<li>' + campo(group) + '</li>');
            }
        }

        if (showUnchecked) {
            $avisoUnchecked.show();
        } else {
            $avisoUnchecked.hide();
        }
    }

    const unirContactos = function unirContactosFormulario(comparativaContactos, idPersona1, idPersona2) {
        let url = URLAPI + '/equipos/update_persona';
        let jsonComparativa = JSON.parse(JSONForm(comparativaContactos));
        jsonComparativa.id = idPersona1;
        jsonComparativa.modificado_por = idUsuario;
        let data = JSON.stringify(jsonComparativa);

        putData(url, data)
            .then((response) => {
                console.log(response);

                if (response.codigo == 200) {
                    Materialize.toast(response.detalles, 4000);
                } else if (response.codigo == 100) {
                    let urlTraspaso = URLAPI + '/equipos/traspasar_registros_persona/' + idPersona1 + '/' + idPersona2;
                    putData(urlTraspaso).then((response) => {
                        if (response.codigo == 200) {
                            Materialize.toast(response.detalles, 4000);
                            reject(true);
                        } else if (response.codigo == 100) {
                            delPers(idPersona2);
                        }
                    })
                }
            })
    }

    const unirPersonaContacto = function unirPersonaContactoFormulario(comparativaContactos, idPersona, idContacto) {
        let jsonComparativa = JSON.parse(JSONForm(comparativaContactos));
        let url = URLAPI + '/equipos/update_persona';
        jsonComparativa.id = idPersona;
        jsonComparativa.modificado_por = idUsuario;
        let data = JSON.stringify(jsonComparativa);

        putData(url, data).then((response) => {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
                reject(true);
            } else if (response.codigo == 100) {
                let urlTraspaso = URLAPI + '/equipos/traspasar_registros_persona_from_temporal/' + idPersona + '/' + idContacto;
                putData(urlTraspaso).then((response) => {
                    if (response.codigo == 200) {
                        Materialize.toast(response.detalles, 4000);
                        reject(true);
                    } else if (response.codigo == 100) {
                        Materialize.toast('Información del contacto transferida correctamente', 2000, '', function() { window.open('contactoControl.php?idPS=' + $idPS, '_self'); });
                    }
                })
            }
        })
    }

    const delPers = function deletePersona(idPersona) {
        let url = URLAPI + '/equipos/contactos/persona/delete/' + idPersona;

        $.post(url, {}, function(response) {
            if (response.codigo == 200) {
                Materialize.toast(response.detalles, 4000);
            } else if (response.codigo == 100) {
                Materialize.toast('Información de persona transferida correctamente', 2000, '', function() { window.open('contactoComprobar.php?idPS=' + $idPS + '&idContacto=' + $idContactoTemporal, '_self'); });
            }
        }, 'json');
    }

    const JSONForm = function JSONObjectFromForm($form) {
        let unindexed_array = $form.serializeArray();
        let indexed_array = {};

        $.map(unindexed_array, function(n, i) {
            indexed_array[n['name']] = n['value'];
        });

        return JSON.stringify(indexed_array);
    }

    const putData = function PUTDataToAPI(url, data) {
        return $.ajax({
            url: url,
            type: 'PUT',
            data: data
        })
    }

    const campo = function devuelveNombreCampo(campo) {
        let arrayCampo = ['id', 'nombre', 'apellido1', 'apellido2', 'telef1', 'telef2', 'movil', 'email', 'tipo_calle', 'calle', 'numero_calle', 'piso', 'cp', 'observaciones', 'dni', 'poblacion', 'provincia', 'nombreCreador', 'fecha_modificado', 'creado_por'];
        let arrayNombre = ['ID', 'Nombre', 'Apellido 1', 'Apellido 2', 'Teléfono 1', 'Teléfono 2', 'Móvil', 'email', 'Tipo calle', 'Calle', 'Número', 'Piso', 'CP', 'Observaciones', 'DNI', 'Población', 'Provincia', 'Creador', 'Fecha Modificado', 'creado_por'];
        let campoIndex = arrayCampo.indexOf(campo);

        if (campoIndex != -1) {
            return arrayNombre[campoIndex];
        }
        return campo;
    }

});