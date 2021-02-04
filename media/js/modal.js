document.addEventListener('DOMContentLoaded' ,function () {

    window.QuantummanagerYoothemepro = {
        fieldWrap: false,
        wrapClick: null
    };

    document.querySelector('body').addEventListener('click', function (ev) {
        QuantummanagerYoothemepro.wrapClick = ev.target;
        checkModal();
    });


    function checkModal() {
        setTimeout(function () {
            let element = null;
            let elements = document.querySelectorAll('.uk-modal');

            for (let i=elements.length-1;i>=0;i--) {
                let flag = true;
                if(elements[i].classList.contains('quantummanageryoothemepro-select')) {
                    flag = false;
                }

                if(elements[i].getAttribute('id') === 'jYProExtraModal') {
                    flag = false;
                }

                if(flag)
                {
                    element = elements[i];
                }
            }

            if (element !== null) {
                let check = element.innerHTML.indexOf('>files</a>') !== -1;
                QuantummanagerYoothemepro.fieldWrap = QuantummanagerYoothemepro.wrapClick.closest('div');

                if (QuantummanagerYoothemepro.fieldWrap.classList.contains('uk-dropdown')) {
                    check = false;
                }

                if (QuantummanagerYoothemepro.wrapClick.classList.contains('uk-button')) {
                    check = false;
                }

                if (QuantummanagerYoothemepro.wrapClick.classList.contains('uk-modal')) {
                    check = false;
                }

                if (QuantummanagerYoothemepro.wrapClick.closest('.uk-modal') !== null) {
                    check = false;
                }

                if (check) {
                    element.classList.add('uk-hidden');
                    UIkit.modal(element, {stack: true}).hide();
                    if (
                        QuantummanagerYoothemepro.fieldWrap.classList.contains('yo-thumbnail') ||
                        QuantummanagerYoothemepro.fieldWrap.classList.contains('uk-position-center-right')
                    ) {
                        QuantummanagerYoothemepro.fieldWrap = QuantummanagerYoothemepro.fieldWrap.parentElement;
                    }

                    showModalSelect();
                }
            }
        }, 100);
    }

    if(window.YooExtendToolbar !== undefined) {
        YooExtendToolbar.appendButton({
            'label': 'Файлы',
            'id': 'files',
            'icon': 'folder',
            'events': {
                'click': function () {
                    showModalFiles();
                }
            },
        });
    }


    function showModalSelect() {

        let modal = document.querySelector('.quantummanageryoothemepro-select');
        if(modal === null) {
            document.querySelector('body').append(
                window.QuantumUtils
                    .createElement('div', {'class': 'uk-modal-container quantummanageryoothemepro-select', 'uk-modal': ''})
                    .addChild('div', {'class': 'uk-modal-dialog'})
                        .addChild('div', {'class': 'uk-modal-body uk-padding-remove'})
                            .add('iframe', {'class': 'uk-width-1-1 uk-height-1-1', 'src': '#', 'style': 'height:' + getHeight() + 'px'})
                            .getParent()
                        .addChild('div', {'class': 'uk-modal-footer uk-text-right'})
                            .add('button', {'class': 'uk-button uk-button-text uk-modal-close uk-margin-right'}, window.QuantumYoothemeproLang.cancel)
                            .add('button', {'class': 'uk-button uk-button-primary button-insert'}, window.QuantumYoothemeproLang.insert)
                            .getParent()
                        .getParent()
                    .build()
            );

            modal = document.querySelector('.quantummanageryoothemepro-select');
            QuantummanagerYoothemepro.modal = modal;
            QuantummanagerYoothemepro.modal.querySelector('iframe').setAttribute('src', 'index.php?option=com_ajax&plugin=quantumyoothemepro&group=system&format=html&tmpl=component')
        }

        UIkit.modal(modal).show();
    }


    function showModalFiles() {

        let modal = document.querySelector('.quantummanageryoothemepro-files');
        if(modal === null) {
            let append = document.querySelector('.uk-noconflict');
            if(append === null) {
                append = document.querySelector('body');
            }

            append.append(
                QuantumUtils
                    .createElement('div', {'class': 'uk-modal-container quantummanageryoothemepro-files', 'uk-modal': ''})
                    .addChild('div', {'class': 'uk-modal-dialog'})
                        .addChild('div', {'class': 'uk-modal-body uk-padding-remove'})
                            .add('iframe', {'class': 'uk-width-1-1 uk-height-1-1', 'src': 'index.php?option=com_quantummanager&layout=modal&tmpl=component', 'style': 'height:' + getHeight() + 'px'})
                            .getParent()
                        .getParent()
                    .build()
            );

            modal = document.querySelector('.quantummanageryoothemepro-files');
        }

        UIkit.modal(modal).show();
    }


    function getHeight() {
        let height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        height -= 170;
        return height;
    }


});