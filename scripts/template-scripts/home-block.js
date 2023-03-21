document.addEventListener("readystatechange", event => {
    if (event.target.readyState === "interactive") {
        let odd = true;
        for (let element of document.getElementsByClassName("home-block")) {
            if (odd) {
                element.classList.add("home-block-left");
                odd = !odd;
            } else {
                element.classList.add("home-block-right");
                odd = !odd;
            }
        }
    }
})
