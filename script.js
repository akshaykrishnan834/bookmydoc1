function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function (event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
};

// Handle form submissions
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    alert('Login functionality will be implemented here');
    closeModal('loginModal');
});

document.getElementById('signupForm').addEventListener('submit', function (e) {
    e.preventDefault();
    alert('Signup functionality will be implemented here');
    closeModal('signupModal');
});
