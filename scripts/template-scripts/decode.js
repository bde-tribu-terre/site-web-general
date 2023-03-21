function decoder(target, type_balise) {
    let elementBase64 = document.getElementById(target + '_base64');
    let strBase64 = elementBase64.innerText;
    let strMail = atob(strBase64);
    let elementDiv = document.getElementById(target + '_div');
    elementDiv.removeChild(elementBase64);

    let elementMail = document.createElement(type_balise);
    elementMail.setAttribute('id', target + '_mail');
    elementMail.innerText = strMail;
    elementDiv.appendChild(elementMail);

    let elementButton = document.getElementById(target + '_button');
    elementButton.setAttribute('disabled', '');
}
