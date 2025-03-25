/**
 * Realiza y procesa la petición asíncrona con estructura basada en FormData
 */
async function autenticar() {
    // Construir los datos de la petición como un objeto JSON
    let datosPeticion = {
        "action": "login",
        "data": {
            "username": document.getElementById("txtUserName").value,
            "password": document.getElementById("txtPassword").value
        }
    };

    // Convertir el objeto JSON a una cadena de texto
    let datosJSON = JSON.stringify(datosPeticion);

    // Preparar los datos a enviar en un objeto FormData
    let datosFormData = new FormData();
    datosFormData.append('peticion', datosJSON);

    // Generar el URL al que vamos a realizar la solicitud
    let url = "login.php";

    try {
        // Realizar la petición asíncrona
        let response = await fetch(url, {
            method: 'POST',
            body: datosFormData // Enviar los datos FormData
        });
        
        // Procesar la respuesta
        let respuesta = await response.json(); // Convertir el cuerpo de la respuesta a JSON
        
            if (respuesta.success === false) {
                // Manejar credenciales incorrectas
                alert(respuesta.data.message || "Credenciales incorrectas");
            } else if (respuesta.success === true) {
                // Manejar credenciales correctas
                document.getElementById("login").classList.add("oculto");
                document.getElementById("logged").classList.remove("oculto");

                // Crear dinámicamente el avatar
                let avatarDiv = document.getElementById("avatar");
                while (avatarDiv.firstChild) {
                    avatarDiv.removeChild(avatarDiv.firstChild); // Limpiar contenido anterior
                }
                let avatarImg = document.createElement("img");
                avatarImg.src = `data:image/png;base64,${respuesta.data.avatar}`;
                avatarImg.alt = "Avatar";
                avatarDiv.appendChild(avatarImg);

                // Crear dinámicamente el saludo
                let nombreDiv = document.getElementById("nombre");
                nombreDiv.textContent = `${respuesta.data.name} ${respuesta.data.lastname}`;

                // Crear dinámicamente los mensajes
                let mensajesDiv = document.getElementById("mensajes");
                while (mensajesDiv.firstChild) {
                    mensajesDiv.removeChild(mensajesDiv.firstChild); // Limpiar mensajes anteriores
                }
                let header = document.createElement("h4");
                header.textContent = `Tiene ${respuesta.messages.length} mensajes nuevos`;
                mensajesDiv.appendChild(header);

                for (let mensaje of respuesta.messages) {
                    let mensajeDiv = document.createElement("div");
                    mensajeDiv.classList.add("message-box");

                    let mensajeH4 = document.createElement("h4");
                    mensajeH4.textContent = mensaje.subject;

                    let mensajeFecha = document.createElement("p");
                    mensajeFecha.textContent = mensaje.date;

                    let mensajeCuerpo = document.createElement("p");
                    mensajeCuerpo.textContent = mensaje.body;

                    mensajeDiv.appendChild(mensajeH4);
                    mensajeDiv.appendChild(mensajeFecha);
                    mensajeDiv.appendChild(mensajeCuerpo);
                    mensajesDiv.appendChild(mensajeDiv);
                }
            }
    } catch (error) {
        console.log("Error" + error);
    }
}

/**
 * Función principal
 */
function main() {
    const boton = document.getElementById("btnLogin");
    boton.addEventListener("click", autenticar);
}

// Tras cargarse el DOM, llamar a main()
window.addEventListener("load", main);
