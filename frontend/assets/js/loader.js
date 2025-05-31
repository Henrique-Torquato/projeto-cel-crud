            // ================================================
      // Função para ocultar o loader após carregamento
      // ================================================
      document.addEventListener("DOMContentLoaded", function () {
        const loaderWrapper = document.getElementById("loader-wrapper");
        if (loaderWrapper) {
          setTimeout(() => {
            loaderWrapper.style.display = "none";
          }, 500);
        }
      });