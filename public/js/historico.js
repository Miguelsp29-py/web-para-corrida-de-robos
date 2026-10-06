const tempoAzul = localStorage.getItem("tempoAzul");
const tempoVermelho = localStorage.getItem("tempoVermelho");
const nomeAzul = localStorage.getItem("nomeAzul");
const nomeVermelho = localStorage.getItem("nomeVermelho");
const ganhador = localStorage.getItem("ganhador");
const partidaId = localStorage.getItem("idPartida");

document.getElementById("historico-tempo1").textContent =
    tempoAzul || "--";
document.getElementById("historico-tempo2").textContent =
    tempoVermelho || "--";
document.getElementById("historico-partida").textContent =
    partidaId ? `#${partidaId}` : "--";

if (ganhador === "1") {
    document.getElementById("historico-equipe1").textContent =
        `${nomeAzul || "Equipe azul"} 👑`;
    document.getElementById("historico-equipe2").textContent =
        nomeVermelho || "Equipe vermelha";
} else if (ganhador === "2") {
    document.getElementById("historico-equipe2").textContent =
        `${nomeVermelho || "Equipe vermelha"} 👑`;
    document.getElementById("historico-equipe1").textContent =
        nomeAzul || "Equipe azul";
} else {
    document.getElementById("historico-equipe1").textContent =
        nomeAzul || "Equipe azul";
    document.getElementById("historico-equipe2").textContent =
        nomeVermelho || "Equipe vermelha";
}
