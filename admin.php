<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f3f4f6;
        color: #333;
    }

    .container {
        padding: 20px;
        max-width: 1500px;
        margin: 0 auto;
    }

    h1 {
        text-align: center;
        color: #4a5568;
        margin-bottom: 20px;
    }

    select {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 0 auto 20px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }

    select:focus {
        outline: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 10px;
    }

    .card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        padding:20px;
        transition:all 1s;
    }
    .transparente{
    	opacity:0.2;
    }
    .card:hover{
    	opacity:1;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .card img {
        max-width: 100%;
        height: auto;
        border-bottom: 2px solid #f3f4f6;
    }

    .username {
        font-size: 1.3em;
        font-weight: bold;
        margin: 15px 0;
        color: #2d3748;
    }

    .slider-container {
        margin: 15px 10px;
    }

    input[type="range"] {
        width: 100%;
        -webkit-appearance: none;
        background: #edf2f7;
        border-radius: 5px;
        height: 6px;
        outline: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #4a90e2;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background 0.3s ease, transform 0.2s ease;
    }

    input[type="range"]::-webkit-slider-thumb:hover {
        background: #2563eb;
        transform: scale(1.2);
    }

    input[type="range"]::-moz-range-thumb {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #4a90e2;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background 0.3s ease, transform 0.2s ease;
    }

    input[type="range"]::-moz-range-thumb:hover {
        background: #2563eb;
        transform: scale(1.2);
    }

    .date {
        font-size: 0.9em;
        color: #718096;
        margin-top: 10px;
    }
</style>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        max-width: 90%;
        max-height: 80%;
        margin: auto;
        display: block;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    }

    .modal-caption {
        text-align: center;
        color: #fff;
        font-size: 1.2em;
        margin-top: 15px;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 25px;
        color: #fff;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
    }
    .progress-container {
    margin-top: 10px;
    width: 100%;
    height: 20px;
    background-color: #e2e8f0; /* Light grey for the 24-hour background */
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-container {
    margin-top: 10px;
    width: 100%;
    height: 20px;
    background-color: #e2e8f0; /* Light grey for the 24-hour background */
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-bar {
position:absolute;
    height: 100%;
    background-color: #38a169; /* Green for the active period */
    width: 0%; /* Will be dynamically updated */
    transition: width 0.5s ease;
}

.progress-time {
    margin-top: 5px;
    font-size: 0.8em;
    color: #4a5568;
    text-align: center;
}

</style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <select id="roomSelector">
            <option value="">Selecciona una sala</option>
            <option value="DAM">DAM</option>
            <option value="SMR">SMR</option>
        </select>
        <div class="grid" id="grid"></div>
    </div>
	<div id="modal" class="modal">
			 <span id="closeModal" class="close">&times;</span>
			 <img id="modalImage" class="modal-content" alt="Fullscreen View">
			 <div id="modalCaption" class="modal-caption"></div>
		</div>
    <script>
        const roomSelector = document.getElementById("roomSelector");
			const grid = document.getElementById("grid");
			
			const modal = document.getElementById("modal");
const modalImage = document.getElementById("modalImage");
const modalCaption = document.getElementById("modalCaption");
const closeModal = document.getElementById("closeModal");

function calculateHourRange(firstTimestamp, lastTimestamp) {
    const startHour = new Date(firstTimestamp * 1000).getHours();
    const endHour = new Date(lastTimestamp * 1000).getHours();
    const startPercentage = (startHour / 24) * 100;
    const endPercentage = (endHour / 24) * 100;
    return { start: startPercentage, end: endPercentage };
}

			async function fetchStudentData(room) {
    if (!room) {
        grid.innerHTML = "<p>Por favor, selecciona una sala.</p>";
        return;
    }

    try {
        const response = await fetch(`adminback.php?room=${room}`);
        const data = await response.json();

        grid.innerHTML = ""; // Clear existing cards

        data.forEach(async (student) => {
            const studentCard = document.createElement("div");
            studentCard.classList.add("card");
            studentCard.setAttribute("data-username", student.username);

            const studentImages = await fetchStudentImages(room, student.username);

            if (!studentImages.length) return;

            // Create elements for the existing content
            const imageElement = document.createElement("img");
            const usernameElement = document.createElement("div");
            const sliderContainer = document.createElement("div");
            const dateLabel = document.createElement("div");

            imageElement.src = studentImages[studentImages.length - 1].path;
            imageElement.alt = `${student.username}'s Screen`;

            usernameElement.classList.add("username");
            usernameElement.innerText = student.username;

            sliderContainer.classList.add("slider-container");
            const slider = document.createElement("input");
            slider.type = "range";
            slider.min = 0;
            slider.max = studentImages.length - 1;
            slider.value = studentImages.length - 1;

            dateLabel.classList.add("date");
            dateLabel.innerText = new Date(
                studentImages[studentImages.length - 1].timestamp * 1000
            ).toLocaleString();

            slider.addEventListener("input", (e) => {
                const index = e.target.value;
                const selectedImage = studentImages[index];
                imageElement.src = selectedImage.path;
                dateLabel.innerText = new Date(
                    selectedImage.timestamp * 1000
                ).toLocaleString();
            });

            sliderContainer.appendChild(slider);

            // Create and add the 24-hour progress bar
            const firstTimestamp = studentImages[0].timestamp;
            const lastTimestamp = studentImages[studentImages.length - 1].timestamp;

            const { start, end } = calculateHourRange(firstTimestamp, lastTimestamp);

            const progressContainer = document.createElement("div");
            progressContainer.classList.add("progress-container");

            const progressBar = document.createElement("div");
            progressBar.classList.add("progress-bar");
            progressBar.style.left = `${start}%`;
            progressBar.style.width = `${end - start}%`;

            const progressTimeLabel = document.createElement("div");
            progressTimeLabel.classList.add("progress-time");
            progressTimeLabel.innerText = `Active: ${new Date(
                firstTimestamp * 1000
            ).getHours()}h - ${new Date(lastTimestamp * 1000).getHours()}h`;

            progressContainer.appendChild(progressBar);

            // Append all elements to the student card
            studentCard.appendChild(usernameElement);
            studentCard.appendChild(imageElement);
            studentCard.appendChild(sliderContainer);
            studentCard.appendChild(dateLabel);
            studentCard.appendChild(progressContainer);
            studentCard.appendChild(progressTimeLabel);

            grid.appendChild(studentCard);
        });

        setInterval(() => checkForUpdates(room), 60000);
    } catch (err) {
        console.error("Error fetching student data:", err);
    }
}

			async function fetchStudentImages(room, username) {
				 try {
					  const response = await fetch(`adminimages.php?room=${room}&user=${username}`);
					  return await response.json();
				 } catch (err) {
					  console.error("Error fetching images for student:", err);
					  return [];
				 }
			}

			async function checkForUpdates(room) {
    if (!room) return;

    try {
        console.log("Checking for updates...");
        const response = await fetch(`adminback.php?room=${room}`);
        const data = await response.json();

        data.forEach(async (student) => {
            const studentCard = document.querySelector(`.card[data-username="${student.username}"]`);
            if (!studentCard) return;

            const studentImages = await fetchStudentImages(room, student.username);
            if (!studentImages.length) return;

            const lastImage = studentImages[studentImages.length - 1];
            const lastUpdateTime = new Date(lastImage.timestamp * 1000);

            // Update the image, slider, and date in the DOM
            const imageElement = studentCard.querySelector("img");
            const dateLabel = studentCard.querySelector(".date");
            const slider = studentCard.querySelector("input[type='range']");

            // Update image if it's different
            if (imageElement.src !== lastImage.path) {
                imageElement.src = `${lastImage.path}?t=${Date.now()}`; // Cache-busting
                console.log(`Updated image for ${student.username}`);
            }

            // Update the slider values
            slider.max = studentImages.length - 1;
            slider.value = studentImages.length - 1;

            // Update the last capture date
            dateLabel.innerText = lastUpdateTime.toLocaleString();

            // Ensure slider functionality reflects updated images
            slider.oninput = (e) => {
                const index = e.target.value;
                const selectedImage = studentImages[index];
                imageElement.src = `${selectedImage.path}?t=${Date.now()}`;
                dateLabel.innerText = new Date(
                    selectedImage.timestamp * 1000
                ).toLocaleString();
            };

            // Update transparency based on time
            const tenMinutesAgo = new Date(Date.now() - 2 * 60 * 1000);
            if (lastUpdateTime < tenMinutesAgo) {
                studentCard.classList.add("transparente");
            } else {
                studentCard.classList.remove("transparente");
            }
        });
    } catch (err) {
        console.error("Error checking for updates:", err);
    }
}


			// Set the interval to check for updates every 10 seconds
			roomSelector.addEventListener("change", () => {
				 const selectedRoom = roomSelector.value;
				 fetchStudentData(selectedRoom);

				 // Clear existing intervals to avoid duplicates
				 clearInterval(window.updateInterval);

				 // Set a new interval for updates
				 if (selectedRoom) {
					  window.updateInterval = setInterval(() => checkForUpdates(selectedRoom), 60000);
				 }
			});
grid.addEventListener("click", (e) => {
    if (e.target.tagName === "IMG") {
        modal.style.display = "flex";
        modal.style.flexDirection = "column";
        modalImage.src = e.target.src;
        const username = e.target.closest(".card").getAttribute("data-username");
        modalCaption.innerText = `${username} - Fullscreen View`;
    }
});

closeModal.addEventListener("click", () => {
    modal.style.display = "none";
});

window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});
    </script>
</body>
</html>

