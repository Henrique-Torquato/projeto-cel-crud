document.addEventListener("DOMContentLoaded", async () => {
    try {
      // URL da API de produtos
      const apiUrl = "http://localhost/projetocel/api/produtos/";

      // Faz a requisição GET para buscar os produtos
      const response = await fetch(apiUrl);
      if (!response.ok) {
        throw new Error("Erro ao buscar produtos.");
      }

      // Converte a resposta em JSON
      const produtos = await response.json();

      // Seleciona o contêiner onde os produtos serão exibidos
      const container = document.getElementById("produtos-container");

      // Variável para armazenar o produto selecionado
      let produtoSelecionado = null;

      // Função para lidar com o clique no botão "Comprar"
      function handleCompraClick(event) {
        // Captura o ID do produto associado ao botão clicado
        const botaoClicado = event.target.closest(".comprar-btn");
        const produtoId = botaoClicado.getAttribute("data-produto-id");

        // Encontra o produto correspondente no array de produtos
        produtoSelecionado = produtos.find((produto) => produto.id == produtoId);

        if (!produtoSelecionado) {
          console.error("Produto não encontrado.");
          return;
        }

        // Preenche o modal com os dados do produto
        document.getElementById("produto-nome").textContent = produtoSelecionado.nome;
        document.getElementById("produto-preco").textContent = formatarPreco(produtoSelecionado.preco);

        // Busca os dados do usuário logado
        const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado")) || JSON.parse(sessionStorage.getItem("usuarioLogado"));
        if (usuarioLogado) {
          document.getElementById("usuario-nome").textContent = usuarioLogado.nome;
          document.getElementById("usuario-endereco").textContent = usuarioLogado.endereco;
          document.getElementById("endereco-entrega").textContent = usuarioLogado.endereco;
        } else {
          alert("Você precisa estar logado para realizar uma compra.");
          return;
        }

        // Exibe o modal
        const checkoutModal = new bootstrap.Modal(document.getElementById("checkoutModal"));
        checkoutModal.show();
      }

      // Adiciona evento de clique ao botão "Confirmar Compra"
      const confirmarCompraButton = document.getElementById("confirmar-compra");
      confirmarCompraButton.addEventListener("click", async () => {
        try {
          if (!produtoSelecionado) {
            console.error("Nenhum produto selecionado.");
            return;
          }

          // Formata a data atual
          const dataAtual = new Date().toISOString().split("T")[0];

          // Monta o corpo da requisição POST no formato correto
          const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado")) || JSON.parse(sessionStorage.getItem("usuarioLogado"));
          const checkoutData = {
            usuario_id: usuarioLogado.id,
            valor_total: parseFloat(produtoSelecionado.preco),
            status: "pendente",
            data_pedido: dataAtual,
            itens: [
              {
                produto_id: produtoSelecionado.id,
                quantidade: 1,
                preco: parseFloat(produtoSelecionado.preco),
              },
            ],
          };

          // Envia os dados do checkout para a API
          const responseCheckout = await fetch("http://localhost/projetocel/api/pedidos", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(checkoutData),
          });

          if (!responseCheckout.ok) {
            throw new Error("Erro ao processar o pedido.");
          }

          alert("Compra realizada com sucesso!");

          // Fecha o modal após a compra
          const checkoutModal = bootstrap.Modal.getInstance(document.getElementById("checkoutModal"));
          checkoutModal.hide();

          // Limpa o produto selecionado
          produtoSelecionado = null;
        } catch (error) {
          console.error("Erro:", error);
          alert("Ocorreu um erro ao processar a compra. Tente novamente mais tarde.");
        }
      });

      // Itera sobre os produtos e cria os elementos HTML dinamicamente
      produtos.forEach((produto) => {
        const card = `
          <div class="col-md-4 mb-4">
            <div class="card overflow-hidden shadow">
              <img class="card-img-top" src="${produto.imagem_url}" alt="${produto.nome}" />
              <div class="card-body py-4 px-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between mb-3">
                  <h4 class="text-secondary fw-medium">${produto.nome}</h4>
                  <span class="fs-1 fw-medium">R$ ${formatarPreco(produto.preco)}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <img src="assets/img/dest/navigation.svg" style="margin-right: 14px" width="20" alt="navigation" />
                  <span class="fs-0 fw-medium">Entrega em todo Brasil</span>
                </div>
                <button class="comprar-btn" data-produto-id="${produto.id}">
                  <img src="assets/img/icons/cart-icon.png" alt="Comprar" />
                  Comprar
                </button>
              </div>
            </div>
          </div>
        `;
        container.innerHTML += card; // Adiciona o card ao contêiner
      });

      // Adiciona evento de clique aos botões "Comprar"
      const botoesComprar = document.querySelectorAll(".comprar-btn");
      botoesComprar.forEach((botao) => {
        botao.addEventListener("click", handleCompraClick);
      });
    } catch (error) {
      console.error("Erro:", error);
      alert("Ocorreu um erro ao carregar os produtos. Tente novamente mais tarde.");
    }
  });

  // Função para formatar o preço com vírgula como separador decimal
  function formatarPreco(preco) {
    return parseFloat(preco).toFixed(2).replace(".", ",");
  }