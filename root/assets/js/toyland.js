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
