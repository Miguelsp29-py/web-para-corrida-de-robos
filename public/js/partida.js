const apiBaseUrl = window.API_BASE_URL
    || (window.location.port
        ? window.location.origin
        : "http://localhost:8080");
const nomeAzul = localStorage.getItem("nomeAzul");
const nomeVermelho = localStorage.getItem("nomeVermelho");
const tempoAtual = document.getElementById("tempo");
const tempoAzulElement = document.getElementById("tempo-azul");
const tempoVermelhoElement = document.getElementById("tempo-vermelho");
const botaoCronometro = document.getElementById("btn-iniciar/parar");
const botaoFinalizar = document.getElementById("btn-finalizar");
const mensagemFinalizacao = document.getElementById("mensagem-finalizacao");
let inicio = 0;
let intervalo = null;
let cronometroAtivo = false;
let etapa = "azul";

if (!nomeAzul || !nomeVermelho) {
    window.location.replace("competicao.html");
} else {
    document.getElementById("nome-azul").textContent = nomeAzul.toUpperCase();
    document.getElementById("nome-vermelho").textContent = nomeVermelho.toUpperCase();
    botaoCronometro.textContent = "Iniciar robô azul";
}

function formatarTempo(milissegundos) {
    const minutos = Math.floor(milissegundos / 60000);
    const segundos = Math.floor((milissegundos % 60000) / 1000);
    const centesimos = Math.floor((milissegundos % 1000) / 10);

    return `${String(minutos).padStart(2, "0")}:`
        + `${String(segundos).padStart(2, "0")}.`
        + String(centesimos).padStart(2, "0");
}

function tempoEmSegundos(tempoFormatado) {
    const partes = tempoFormatado.match(/^(\d+):(\d{2})\.(\d{2})$/);

    if (!partes) {
        throw new Error("Não foi possível interpretar um dos tempos.");
    }

    return Number(partes[1]) * 60
        + Number(partes[2])
        + Number(partes[3]) / 100;
}

function atualizarCronometro() {
    tempoAtual.textContent = formatarTempo(Date.now() - inicio);
}

botaoCronometro.addEventListener("click", function () {
    mensagemFinalizacao.textContent = "";

    if (!cronometroAtivo) {
        inicio = Date.now();
        cronometroAtivo = true;
        botaoCronometro.textContent = "Parar cronômetro";
        intervalo = window.setInterval(atualizarCronometro, 10);
        return;
    }

    window.clearInterval(intervalo);
    atualizarCronometro();
    cronometroAtivo = false;

    if (etapa === "azul") {
        tempoAzulElement.textContent = tempoAtual.textContent;
        etapa = "vermelho";
        botaoCronometro.textContent = "Iniciar robô vermelho";
        tempoAtual.textContent = "00:00.00";
        return;
    }

    tempoVermelhoElement.textContent = tempoAtual.textContent;
    etapa = "finalizado";
    botaoCronometro.disabled = true;
    botaoFinalizar.disabled = false;
    tempoAtual.textContent = "00:00.00";
});

botaoFinalizar.addEventListener("click", async function () {
    const tempoAzul = tempoAzulElement.textContent;
    const tempoVermelho = tempoVermelhoElement.textContent;

    if (
        !tempoAzul
        || !tempoVermelho
        || tempoAzul === "-"
        || tempoVermelho === "-"
    ) {
        mensagemFinalizacao.textContent =
            "Cronometre os dois robôs antes de finalizar.";
        return;
    }

    botaoFinalizar.disabled = true;
    mensagemFinalizacao.textContent = "Salvando partida...";

    try {
        const response = await fetch(`${apiBaseUrl}/partidas`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                partida: {
                    nomeparticipante1: nomeAzul,
                    nomeparticipante2: nomeVermelho,
                    robo1: "Azul",
                    tempo1: tempoEmSegundos(tempoAzul),
                    robo2: "Vermelho",
                    tempo2: tempoEmSegundos(tempoVermelho)
                }
            })
        });
        const resposta = await response.json();

        if (!response.ok || !resposta.success) {
            throw new Error(
                resposta.message || `Falha ao salvar partida (${response.status}).`
            );
        }

        localStorage.setItem("tempoAzul", tempoAzul);
        localStorage.setItem("tempoVermelho", tempoVermelho);

        const segundosAzul = tempoEmSegundos(tempoAzul);
        const segundosVermelho = tempoEmSegundos(tempoVermelho);

        if (segundosAzul < segundosVermelho) {
            localStorage.setItem("ganhador", "1");
        } else if (segundosVermelho < segundosAzul) {
            localStorage.setItem("ganhador", "2");
        } else {
            localStorage.removeItem("ganhador");
        }

        localStorage.setItem(
            "idPartida",
            String(resposta.data.partidas[0].id)
        );
        window.location.href = "historico.html";
    } catch (error) {
        mensagemFinalizacao.textContent =
            `Não foi possível salvar a partida: ${error.message}`;
        botaoFinalizar.disabled = false;
    }
});