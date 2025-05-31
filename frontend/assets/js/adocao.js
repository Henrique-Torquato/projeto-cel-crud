document.addEventListener("DOMContentLoaded", async () => {
    try {
      // URL da API de pets
      const apiUrl = "http://localhost/projetocel/api/pets/";
  
      // Faz a requisição GET para buscar os animais
      const response = await fetch(apiUrl);
      if (!response.ok) {
        throw new Error("Erro ao buscar animais.");
      }
  
      // Converte a resposta em JSON
      const animais = await response.json();
  
      // Seleciona o contêiner onde os animais serão exibidos
      const container = document.getElementById("animais-container");
  
      // Variável para armazenar o animal selecionado
      let animalSelecionado = null;
  
      // Função para lidar com o clique no botão "Quero adotar"
      function handleAdocaoClick(event) {
        // Captura o ID do animal associado ao botão clicado
        const botaoClicado = event.target.closest(".adotar-btn");
        const animalId = botaoClicado.getAttribute("data-animal-id");
  
        // Encontra o animal correspondente no array de animais
        animalSelecionado = animais.find((animal) => animal.id == animalId);
  
        if (!animalSelecionado) {
          console.error("Animal não encontrado.");
          return;
        }
  
        // Preenche o modal com os dados do animal
        document.getElementById("animal-nome").textContent = animalSelecionado.nome;
        document.getElementById("animal-raca").textContent = animalSelecionado.raca;
        document.getElementById("animal-deficiencia").textContent = animalSelecionado.deficiencia;
        document.getElementById("animal-cuidados").textContent = animalSelecionado.cuidados_especiais;
  
        // Busca os dados do usuário logado
        const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado")) || JSON.parse(sessionStorage.getItem("usuarioLogado"));
        if (usuarioLogado) {
          document.getElementById("usuario-nome").textContent = usuarioLogado.nome;
          document.getElementById("usuario-endereco").textContent = usuarioLogado.endereco;
          document.getElementById("endereco-entrega").textContent = usuarioLogado.endereco;
        } else {
          alert("Você precisa estar logado para realizar uma adoção.");
          return;
        }
  
        // Exibe o modal
        const adocaoModal = new bootstrap.Modal(document.getElementById("adocaoModal"));
        adocaoModal.show();
      }
  
      // Adiciona evento de clique ao botão "Confirmar Adoção"
      const confirmarAdocaoButton = document.getElementById("confirmar-adocao");
      confirmarAdocaoButton.addEventListener("click", async () => {
        try {
          if (!animalSelecionado) {
            console.error("Nenhum animal selecionado.");
            return;
          }
  
          // Formata a data e hora atual no formato YYYY-MM-DD HH:mm:ss
          const dataAtual = new Date();
          const dataFormatada = `${dataAtual.getFullYear()}-${String(dataAtual.getMonth() + 1).padStart(2, '0')}-${String(dataAtual.getDate()).padStart(2, '0')} ${String(dataAtual.getHours()).padStart(2, '0')}:${String(dataAtual.getMinutes()).padStart(2, '0')}:${String(dataAtual.getSeconds()).padStart(2, '0')}`;
  
          // Monta o corpo da requisição POST no formato correto
          const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado")) || JSON.parse(sessionStorage.getItem("usuarioLogado"));
          const adocaoData = {
            usuario_id: usuarioLogado.id,
            pet_id: animalSelecionado.id,
            data_adocao: dataFormatada,
            status: "pendente",
          };
  
          // Envia os dados da adoção para a API
          const responseAdocao = await fetch("http://localhost/projetocel/api/adocoes", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(adocaoData),
          });
  
          if (!responseAdocao.ok) {
            throw new Error("Erro ao processar a adoção.");
          }
  
          alert("Adoção realizada com sucesso!");
  
          // Fecha o modal após a adoção
          const adocaoModal = bootstrap.Modal.getInstance(document.getElementById("adocaoModal"));
          adocaoModal.hide();
  
          // Limpa o animal selecionado
          animalSelecionado = null;
        } catch (error) {
          console.error("Erro:", error);
          alert("Ocorreu um erro ao processar a adoção. Tente novamente mais tarde.");
        }
      });
  
      // Itera sobre os animais e cria os elementos HTML dinamicamente
      animais.forEach((animal) => {
        const card = `
          <div class="col-md-4 mb-4">
            <div class="card overflow-hidden shadow">
              <img class="card-img-top" src="${animal.foto_url}" alt="${animal.nome}" />
              <div class="card-body py-4 px-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between mb-3">
                  <h4 class="text-secondary fw-medium">${animal.nome}</h4>
                  <span class="fs-1 fw-medium">${animal.idade} anos</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <img src="assets/img/dest/navigation.svg" style="margin-right: 14px" width="20" alt="navigation" />
                  <span class="fs-0 fw-medium">Raça: ${animal.raca}</span>
                </div>
                <p class="text-muted mb-3">Deficiência: ${animal.deficiencia}</p>
                <button class="comprar-btn adotar-btn w-100" data-animal-id="${animal.id}">
                  <img src="assets/img/icons/icon-adocao.png" alt="Adotar" />
                  Quero adotar
                </button>
              </div>
            </div>
          </div>
        `;
        container.innerHTML += card; // Adiciona o card ao contêiner
      });
  
      // Adiciona evento de clique aos botões "Quero adotar"
      const botoesAdotar = document.querySelectorAll(".adotar-btn");
      botoesAdotar.forEach((botao) => {
        botao.addEventListener("click", handleAdocaoClick);
      });
    } catch (error) {
      console.error("Erro:", error);
      alert("Ocorreu um erro ao carregar os animais. Tente novamente mais tarde.");
    }
  });