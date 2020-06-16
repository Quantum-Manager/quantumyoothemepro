document.addEventListener('DOMContentLoaded' ,function () {
    let QuantummanagerYoothemepro = window.parent.QuantummanagerYoothemepro,
        buttonInsert = QuantummanagerYoothemepro.modal.querySelector('.button-insert'),
        pathFile = '';
    buttonInsert.setAttribute('disabled', 'disabled');

    buttonInsert.addEventListener('click', function () {
        let fm = QuantummanagerLists[0];

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(pathFile)
            + '&scope=' + fm.data.scope + '&v=' + QuantumUtils.randomInteger(111111, 999999)))
            .done(function (response) {
                response = JSON.parse(response);
                let input = QuantummanagerYoothemepro.fieldWrap.querySelector('input');
                if(input !== null) {
                    input.focus();
                    input.value = response.path;
                    let evt = document.createEvent("HTMLEvents");
                    evt.initEvent("input");
                    input.dispatchEvent(evt);
                }
                QuantummanagerYoothemepro.fieldWrap = false;
                window.parent.UIkit.modal(QuantummanagerYoothemepro.modal).hide();
            });

    });

    QuantumEventsDispatcher.add('clickObject', function (fm) {
        let file = fm.Quantumviewfiles.objectSelect;

        if(file === undefined) {
            buttonInsert.setAttribute('disabled', 'disabled');
            return;
        }
    });

    QuantumEventsDispatcher.add('clickFile', function (fm) {
        let file = fm.Quantumviewfiles.file;
        if(file === undefined) {
            buttonInsert.setAttribute('disabled', 'disabled');
            return;
        }

        let name = file.querySelector('.file-name').innerHTML;
        pathFile = fm.data.path + '/' + name;
        buttonInsert.removeAttribute('disabled');
    });

    QuantumEventsDispatcher.add('dblclickFile', function (fm, n, el) {
        let name = el.querySelector('.file-name').innerHTML;
        pathFile = fm.data.path + '/' + name;

        let evt = document.createEvent("HTMLEvents");
        evt.initEvent("click");
        buttonInsert.dispatchEvent(evt);
    });

    QuantumEventsDispatcher.add('reloadPaths', function (fm) {
        buttonInsert.setAttribute('disabled', 'disabled');
    });

    QuantumEventsDispatcher.add('updatePath', function (fm) {
        buttonInsert.setAttribute('disabled', 'disabled');
    });

    QuantumEventsDispatcher.add('uploadComplete', function (fm) {

        if(fm.Qantumupload.filesLists.length === 0) {
            return
        }

        let name = fm.Qantumupload.filesLists[0];
        pathFile = fm.data.path + '/' + fm.Qantumupload.filesLists[0];
        buttonInsert.removeAttribute('disabled');
    });



});