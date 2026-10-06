const apiBaseUrl = window.API_BASE_URL
    || (window.location.port
        ? window.location.origin
        : "http://localhost:8080");
const tabelaClassificacao = document.getElementById("tabela-classificacao");
const mensagemResultados = document.getElementById("mensagem-resultados");

function formatarTempo(segundos) {
    const centesimosTotais = Math.round(segundos * 100);
    const minutos = Math.floor(centesimosTotais / 6000);
    const centesimosNoMinuto = centesimosTotais % 6000;
    const segundosNoMinuto = Math.floor(centesimosNoMinuto / 100);
    const centesimos = centesimosNoMinuto % 100;

    return `${String(minutos).padStart(2, "0")}:`
        + `${String(segundosNoMinuto).padStart(2, "0")}.`
        + String(centesimos).padStart(2, "0");
}

function adicionarCelula(linha, texto) {
    const celula = document.createElement("td");
    celula.textContent = texto;
    linha.appendChild(celula);
}

function mostrarMensagemTabela(mensagem) {
    tabelaClassificacao.replaceChildren();
    const linha = document.createElement("tr");
    const celula = document.createElement("td");
    celula.colSpan = 4;
    celula.className = "mensagem-tabela";
    celula.textContent = mensagem;
    linha.appendChild(celula);
    tabelaClassificacao.appendChild(linha);
}

async function carregarResultados() {
    try {
        const [respostaParticipantes, respostaPartidas] = await Promise.all([
            fetch(`${apiBaseUrl}/participantes`),
            fetch(`${apiBaseUrl}/partidas`)
        ]);

        if (!respostaParticipantes.ok || !respostaPartidas.ok) {
            throw new Error("A API não conseguiu carregar os dados da competição.");
        }

        const [dadosParticipantes, dadosPartidas] = await Promise.all([
            respostaParticipantes.json(),
            respostaPartidas.json()
        ]);
        const participantes = dadosParticipantes.data?.participantes;
        const partidas = dadosPartidas.data?.partidas;

        if (
            dadosParticipantes.success !== true
            || dadosPartidas.success !== true
            || !Array.isArray(participantes)
            || !Array.isArray(partidas)
        ) {
            throw new Error("A API retornou dados em um formato inesperado.");
        }

        const classificados = participantes
            .filter((participante) => Number.isFinite(Number(participante.tempo))
                && Number(participante.tempo) > 0)
            .sort((a, b) => Number(a.tempo) - Number(b.tempo))
            .slice(0, 10);

        document.getElementById("total-participantes").textContent =
            String(participantes.length);
        document.getElementById("total-partidas").textContent =
            String(partidas.length);
        document.getElementById("melhor-tempo").textContent =
            classificados.length > 0
                ? formatarTempo(Number(classificados[0].tempo))
                : "--:--.--";

        tabelaClassificacao.replaceChildren();

        if (classificados.length === 0) {
            mostrarMensagemTabela("Ainda não há participantes cadastrados.");
            return;
        }

        classificados.forEach((participante, indice) => {
            const linha = document.createElement("tr");
            adicionarCelula(linha, `${indice + 1}º`);
            adicionarCelula(linha, participante.nome);
            adicionarCelula(linha, `Robô ${participante.robo}`);
            adicionarCelula(linha, formatarTempo(Number(participante.tempo)));
            tabelaClassificacao.appendChild(linha);
        });
    } catch (error) {
        mostrarMensagemTabela("Não foi possível carregar a classificação.");
        mensagemResultados.textContent = error.message;
        mensagemResultados.classList.add("mensagem-resultados-erro");
    }
}

carregarResultados();
