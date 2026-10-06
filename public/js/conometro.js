let inicio;

document.addEventListener("keydown", function(event) {

    if (event.code === "Space") {

        if (!inicio) {
            inicio = Date.now();
        } else {
            let fim = Date.now();
            let tempo = fim - inicio;

            console.log(tempo);
        }

    }

});