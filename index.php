<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AliceBlue - Capturador de pantalla</title>
    <style>
        /* Importar la fuente Ubuntu desde Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap');

        body {
            font-family: 'Ubuntu', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: aliceblue;
            color: #333;
        }

        #container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
        }

        #username, #room {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #007bff;
            border-radius: 5px;
            width: calc(100% - 22px);
            margin-bottom: 10px;
        }

        #startCapture, #stopCapture {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        #startCapture:hover, #stopCapture:hover {
            background-color: #0056b3;
        }

        #stopCapture {
            background-color: #dc3545;
        }

        #stopCapture:hover {
            background-color: #a71d2a;
        }

        #instructions {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        #lastFrameContainer {
            margin-top: 20px;
            text-align: center;
        }

        #lastFrameContainer img {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
        #logo{
        	display:inline;
        	width:60px;
        	position:relative;
        	top:20px;
        }
    </style>
</head>
<body>
    <div id="container">
        <h1><img src="aliceblue.png" id="logo">jocarsa | aliceBlue</h1>
        <p id="instructions">
            Instrucciones:<br>
            1. Introduce tu nombre y selecciona una sala.<br>
            2. Haz clic en "Comenzar" para iniciar la grabación de pantalla.<br>
            3. Haz clic en "Detener" para finalizar la grabación.<br>
            4. Podrás ver el último fotograma capturado en la parte inferior.
        </p>
        <label for="username">Introduce tu nombre:</label>
        <input type="text" id="username" placeholder="Tu nombre" required>
        
        <label for="room">Selecciona una sala:</label>
        <select id="room" required>
            <option value="DAM">DAM</option>
            <option value="SMR">SMR</option>
            <option value="Capitol">Capitol</option>
        </select>
        
        <button id="startCapture">Comenzar</button>
        <button id="stopCapture" style="display: none;">Detener</button>
        <div id="lastFrameContainer" style="display: none;">
            <p>Último fotograma enviado:</p>
            <img id="lastFrame" src="" alt="Último fotograma">
        </div>
    </div>

    <script>
        const startCaptureButton = document.getElementById("startCapture");
        const stopCaptureButton = document.getElementById("stopCapture");
        const lastFrameContainer = document.getElementById("lastFrameContainer");
        const lastFrameImg = document.getElementById("lastFrame");
        let isCapturing = false;
        let captureInterval;

        async function captureScreen() {
            const username = document.getElementById("username").value.trim();
            const room = document.getElementById("room").value;

            if (!username || !room) {
                alert("Por favor, introduce un nombre y selecciona una sala.");
                return;
            }

            try {
                // Request screen capture
                const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                const video = document.createElement("video");
                video.srcObject = stream;
                video.play();

                // Create a canvas to capture frames
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                isCapturing = true;
                startCaptureButton.style.display = "none";
                stopCaptureButton.style.display = "inline-block";

                // Add a confirmation prompt on window close
                window.addEventListener("beforeunload", preventWindowClose);

                // Start frame capturing every 60 seconds
                captureInterval = setInterval(() => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convert the frame to Base64
                    const frame = canvas.toDataURL("image/jpeg");

                    // Update the displayed frame
                    lastFrameImg.src = frame;
                    lastFrameContainer.style.display = "block";

                    // Send the frame to the server
                    fetch("save_frame.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ 
                            room, 
                            username, 
                            frame 
                        }),
                    }).then(response => {
                        if (!response.ok) {
                            console.error("Error al enviar el fotograma.");
                        }
                    }).catch(err => console.error(err));
                }, 60000);

            } catch (err) {
                console.error("Error capturando la pantalla:", err);
            }
        }

        function stopCapture() {
            isCapturing = false;

            // Clear the interval to stop capturing frames
            clearInterval(captureInterval);

            // Remove the confirmation prompt
            window.removeEventListener("beforeunload", preventWindowClose);

            startCaptureButton.style.display = "inline-block";
            stopCaptureButton.style.display = "none";

            alert("La grabación ha sido detenida.");
        }

        function preventWindowClose(event) {
            if (isCapturing) {
                event.preventDefault();
                event.returnValue = "La grabación está en curso. ¿Estás seguro de que deseas salir?";
                return event.returnValue;
            }
        }

        startCaptureButton.addEventListener("click", captureScreen);
        stopCaptureButton.addEventListener("click", stopCapture);
    </script>
</body>
</html>

