// Toy Land Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Confetti function for fun interactions
    function launchConfetti() {
        const emojis = ['🌟','🎈','🎉','✨','💃','🥳','🎊','🦄','🌈','🎯','🧸','🎁'];
        for (let i = 0; i < 40; i++) {
            const span = document.createElement('span');
            span.innerText = emojis[Math.floor(Math.random()*emojis.length)];
            span.className = 'confetti';
            span.style.left = Math.random()*100 + 'vw';
            span.style.animationDelay = Math.random() * 2 + 's';
            document.body.appendChild(span);
            setTimeout(() => span.remove(), 3000);
        }
    }
    
    // Add confetti to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart, .btn-primary');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Only trigger confetti for specific actions
            if (this.textContent.includes('Add to Cart') || 
                this.textContent.includes('Subscribe') ||
                this.textContent.includes('Submit')) {
                launchConfetti();
            }
        });
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('mobile-active');
        });
    }
    
    // Product card hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Form validation improvements
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '#fceabb';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Show cute error message
                showMessage('🤖 Oops! Please fill in all required fields', 'error');
            } else {
                // Success confetti for form submissions
                launchConfetti();
                showMessage('🎉 Awesome! Your form is being processed', 'success');
            }
        });
    });
    
    // Message display function
    function showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type} floating-message`;
        messageDiv.innerHTML = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            padding: 15px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            background: ${type === 'error' ? '#dc3545' : '#f93c64'};
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => messageDiv.remove(), 300);
        }, 3000);
    }
    
    // Add CSS for message animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
});

// Global confetti function for use in HTML
function launchConfetti() {
    const emojis = ['🌟','🎈','🎉','✨','💃','🥳','🎊','🦄','🌈','🎯','🧸','🎁'];
    for (let i = 0; i < 60; i++) {
        const span = document.createElement('span');
        span.innerText = emojis[Math.floor(Math.random()*emojis.length)];
        span.className = 'confetti';
        span.style.left = Math.random()*100 + 'vw';
        span.style.animationDelay = Math.random() * 2 + 's';
        document.body.appendChild(span);
        setTimeout(() => span.remove(), 3000);
    }
}
