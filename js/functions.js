// Example: Function to show a modal (you'll need to add HTML for the modal)
function showModal(title, content) {
    const modal = document.getElementById('myModal');
    const modalTitle = document.querySelector('#myModal .modal-title');
    const modalBody = document.querySelector('#myModal .modal-body');

    modalTitle.textContent = title;
    modalBody.innerHTML = content; // Use innerHTML to allow HTML content

    modal.style.display = 'block'; // Show the modal

    // Add a close button event listener (you'll need a close button in the modal HTML)
    const closeButton = document.querySelector('#myModal .close');
    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });
}

// Example: Function to make an AJAX request
async function fetchData(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Fetch error:", error);
        return null;
    }
}

//Other Helper Functions