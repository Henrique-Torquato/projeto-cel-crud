// ================================================
      // Função para verificar o estado de login ao carregar a página
      // ================================================
      document.addEventListener("DOMContentLoaded", function () {
        const usuarioLogadoLocalStorage = localStorage.getItem("usuarioLogado");
        const usuarioLogadoSessionStorage = sessionStorage.getItem("usuarioLogado");
    
        const loginLink = document.querySelector('a[data-bs-target="#loginModal"]'); // Botão Login
        const cadastroLink = document.querySelector('a[data-bs-target="#cadastroModal"]'); // Botão Cadastre-se
    
        let usuarioLogado = null;
    
        if (usuarioLogadoLocalStorage) {
          usuarioLogado = JSON.parse(usuarioLogadoLocalStorage);
        } else if (usuarioLogadoSessionStorage) {
          usuarioLogado = JSON.parse(usuarioLogadoSessionStorage);
        }
    
        if (usuarioLogado) {
          const nomeUsuario = document.createElement("span");
          nomeUsuario.className = "fw-medium text-primary";
          nomeUsuario.textContent = `Olá, ${usuarioLogado.nome.split(" ")[0]}!`;
    
          const logoutButton = document.createElement("button");
          logoutButton.className = "btn btn-outline-danger ms-3";
          logoutButton.id = "logoutButton";
          logoutButton.textContent = "Sair";
    
          if (loginLink && cadastroLink) {
            loginLink.parentElement.replaceChild(nomeUsuario, loginLink);
            cadastroLink.parentElement.replaceChild(logoutButton, cadastroLink);
          }
    
          logoutButton.addEventListener("click", function () {
            localStorage.removeItem("usuarioLogado");
            sessionStorage.removeItem("usuarioLogado");
            alert("Você foi desconectado.");
            window.location.reload();
          });
        } else {
          if (loginLink && cadastroLink) {
            loginLink.style.display = "inline-block";
            cadastroLink.style.display = "inline-block";
          }
        }
      });
    
      // ================================================
      // Função para lidar com o formulário de login
      // ================================================
      document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.getElementById("loginForm");
    
        loginForm.addEventListener("submit", async function (event) {
          event.preventDefault();
    
          const email = document.getElementById("email").value;
          const password = document.getElementById("password").value;
          const rememberMe = document.getElementById("rememberMe").checked;
    
          try {
            const response = await fetch(`http://localhost/projetocel/api/usuarios?timestamp=${Date.now()}`);
            if (!response.ok) throw new Error("Erro ao buscar usuários.");
    
            const usuarios = await response.json();
            const usuarioEncontrado = usuarios.find(
              (user) => user.email.trim().toLowerCase() === email.trim().toLowerCase() && user.senha === password
            );
    
            if (usuarioEncontrado) {
              if (rememberMe) {
                localStorage.setItem("usuarioLogado", JSON.stringify(usuarioEncontrado));
              } else {
                sessionStorage.setItem("usuarioLogado", JSON.stringify(usuarioEncontrado));
              }
              window.location.href = "index.html";
            } else {
              alert("Email ou senha incorretos. Tente novamente.");
            }
          } catch (error) {
            console.error("Erro:", error);
            alert("Ocorreu um erro ao processar o login. Tente novamente mais tarde.");
          }
        });
      });
    
      // ================================================
      // Função para lidar com o formulário de cadastro
      // ================================================
      document.addEventListener("DOMContentLoaded", function () {
        const cadastroForm = document.getElementById("cadastroForm");
    
        cadastroForm.addEventListener("submit", async function (event) {
          event.preventDefault();
    
          const nome = document.getElementById("nome").value.trim();
          const email = document.getElementById("emailCadastro").value.trim().toLowerCase();
          const senha = document.getElementById("senhaCadastro").value;
          const telefone = document.getElementById("telefone").value.trim();
          const endereco = document.getElementById("endereco").value.trim();
    
          if (!nome || !email || !senha || !telefone || !endereco) {
            alert("Todos os campos são obrigatórios.");
            return;
          }
    
          try {
            const responseGet = await fetch("http://localhost/projetocel/api/usuarios");
            if (!responseGet.ok) throw new Error("Erro ao buscar usuários.");
    
            const usuarios = await responseGet.json();
            const emailExistente = usuarios.some((user) => user.email === email);
            const telefoneExistente = usuarios.some((user) => user.telefone === telefone);
    
            if (emailExistente) {
              alert("Este e-mail já está cadastrado.");
              return;
            }
    
            if (telefoneExistente) {
              alert("Este telefone já está cadastrado.");
              return;
            }
    
            const responsePost = await fetch("http://localhost/projetocel/api/usuarios", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ nome, email, senha, telefone, endereco }),
            });
    
            if (!responsePost.ok) throw new Error("Erro ao cadastrar usuário.");
    
            alert("Cadastro realizado com sucesso!");
            window.location.reload();
          } catch (error) {
            console.error("Erro:", error);
            alert("Ocorreu um erro ao processar o cadastro. Tente novamente mais tarde.");
          }
        });
      });