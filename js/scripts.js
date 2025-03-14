// Navbar Toggle for Mobile
document.querySelector('.nav-toggle').addEventListener('click', () => {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
});

// Smooth Scrolling for Anchor Links
document.querySelectorAll('.navbar a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Form Validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        let isValid = true;
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.style.borderColor = 'red';
                input.nextElementSibling?.remove(); // Remove previous error
                const error = document.createElement('span');
                error.textContent = `${input.name} is required`;
                error.style.color = 'red';
                error.style.fontSize = '0.9rem';
                input.insertAdjacentElement('afterend', error);
            } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                isValid = false;
                input.style.borderColor = 'red';
                input.nextElementSibling?.remove();
                const error = document.createElement('span');
                error.textContent = 'Invalid email format';
                error.style.color = 'red';
                error.style.fontSize = '0.9rem';
                input.insertAdjacentElement('afterend', error);
            } else if (input.type === 'password' && input.value.length < 6) {
                isValid = false;
                input.style.borderColor = 'red';
                input.nextElementSibling?.remove();
                const error = document.createElement('span');
                error.textContent = 'Password must be at least 6 characters';
                error.style.color = 'red';
                error.style.fontSize = '0.9rem';
                input.insertAdjacentElement('afterend', error);
            } else {
                input.style.borderColor = '#ccc';
                input.nextElementSibling?.remove();
            }
        });
        if (!isValid) {
            e.preventDefault();
        }
    });
});