var decidir = null;

window.addEventListener("DOMContentLoaded", function () {
    intializeExample();
}, false);

function intializeExample() {

    let element = document.querySelectorAll('form[name=token-form');
    for (var i = 0; element.length > i; i++) {
        let form = element[i];
        addEvent(form, 'submit', sendForm)
    }
}

function addEvent(el, eventName, handler) {
    if (el.addEventListener) {
        el.addEventListener(eventName, handler);
    } else {
        el.attachEvent('on' + eventName, function () {
            handler.call(el);
        });
    }
}

function sdkResponseHandler(status, response) {

    let resultado = document.querySelector('#resultado');

    cleanHtmlElement(resultado);

    if (status != 200 && status != 201) {
        alert('Error! code: ' + status + ' - response: ' + JSON.stringify(response))
    } else {
        console.log('OK - Respuesta: ' + response);
        console.log('TOKEN: ' + response.id);
        sendPaymentData(response);
        createHtmlListFromObject(response, resultado)
    }
}

function createHtmlListFromObject(object, parentElement) {
    let ul = document.createElement('ul');
    for (let prop in object) {
        let li = document.createElement('li');
        let spanLabel = document.createElement('span');
        let spanValue = document.createElement('span');
        spanLabel.innerText = prop + ': ';
        if (typeof (object[prop]) === 'object') {
            createHtmlListFromObject(object[prop], spanValue);
        } else {
            spanValue.innerText = object[prop];
        }
        li.appendChild(spanLabel);
        li.appendChild(spanValue);
        ul.appendChild(li);
    }
    parentElement.appendChild(ul)
}

function cleanHtmlElement(element) {
    element.innerText = ''
}

function sendForm(event) {
    event.preventDefault();

    var $form = document.querySelector('form[name=token-form');

    let api_url = 'https://developers.decidir.com/api/v2';
    let api_key = 'e9cdb99fff374b5f91da4480c8dca741';

    decidir = new Decidir(api_url, true);
    decidir.setPublishableKey(api_key);
    decidir.setTimeout(10000);

    console.log('Decidir.createToken()');
    decidir.createToken($form, sdkResponseHandler);
    return false;
}

function sendPaymentData(response) {
    console.log(response);
    $('#card_data_form').fadeOut('slow', function () {
        $.ajax({
            type: "POST",
            headers: {"cache-control": "no-cache"},
            url: "process_payment.php",
            async: true,
            dataType: "json",
            data: {
                token: response.id,
                bin: response.bin
            },
            success: function (response) {
                console.log(response);

                if (response.status == "approved") {
                    console.log('Approved ' + response + ' - Estado: ' + response.status);
                    createHtmlListFromObject(response, paymentResult)
                } else {
                    var error = document.getElementById("paymentResult");
                    error.innerHTML = " <span>" + 'Estado :' + response.status + ' <br>Mensaje: ' + response.message + "</span>";

                }
            },
            error: function (e, status) {
                console.log('Error: ' + response);
            }
        });
    });
}