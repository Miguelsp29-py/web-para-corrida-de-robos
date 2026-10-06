const equipeAzul = document.getElementById("equipe1");
const equipeVermelha = document.getElementById("equipe2");
const mensagemPartida = document.getElementById("mensagem-partida");

document
    .getElementById("btn-iniciar-partida")
    .addEventListener("click", async function () {
        const nomeAzul = equipeAzul.value.trim();
        const nomeVermelho = equipeVermelha.value.trim();
        const nomeAzulValido = nomeAzul.length >= 3 && nomeAzul.length <= 40;
        const nomeVermelhoValido =
            nomeVermelho.length >= 3 && nomeVermelho.length <= 40;

        if (!nomeAzulValido || !nomeVermelhoValido) {
            mensagemPartida.textContent =
                "Informe os dois nomes com pelo menos 3 e no máximo 40 caracteres.";
            return;
        }

        const nomeAzulNormalizado = nomeAzul.toLocaleLowerCase("pt-BR");
        const nomeVermelhoNormalizado =
            nomeVermelho.toLocaleLowerCase("pt-BR");

        if (nomeAzulNormalizado === nomeVermelhoNormalizado) {
            mensagemPartida.textContent =
                "Cada participante precisa ter um nome diferente.";
            return;
        }

        const apiBaseUrl = window.API_BASE_URL
            || (window.location.port
                ? window.location.origin
                : "http://localhost:8080");
        const botaoIniciar = document.getElementById("btn-iniciar-partida");
        botaoIniciar.disabled = true;
        mensagemPartida.textContent = "Verificando os nomes...";

        try {
            const response = await fetch(`${apiBaseUrl}/participantes`);
            const resposta = await response.json();

            if (!response.ok || resposta.success !== true) {
                throw new Error(
                    resposta.message || "Não foi possível verificar os nomes."
                );
            }

            const nomesCadastrados = new Set(
                resposta.data.participantes.map((participante) =>
                    participante.nome.toLocaleLowerCase("pt-BR")
                )
            );

            if (
                nomesCadastrados.has(nomeAzulNormalizado)
                || nomesCadastrados.has(nomeVermelhoNormalizado)
            ) {
                mensagemPartida.textContent =
                    "Um desses nomes já está cadastrado. Escolha nomes diferentes.";
                return;
            }

            localStorage.setItem("nomeAzul", nomeAzul);
            localStorage.setItem("nomeVermelho", nomeVermelho);
            window.location.href = "partida.html";
        } catch (error) {
            mensagemPartida.textContent =
                `Não foi possível validar os nomes: ${error.message}`;
        } finally {
            botaoIniciar.disabled = false;
        }
    });
