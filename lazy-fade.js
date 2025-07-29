document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img.lazy-fade');
    const groups = document.querySelectorAll('.group-fade-in');

    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -10% 0px', // Trigger earlier
        threshold: 0.1 // Trigger when 10% is visible
    };

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.onload = () => img.classList.add('fade-in');
                observer.unobserve(img);
            }
        });
    }, observerOptions);

    lazyImages.forEach(img => imageObserver.observe(img));

    const groupObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    });

    groups.forEach(group => groupObserver.observe(group));

    // Handle elements near the bottom of the page
    function checkBottomElements() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // If we're within 100px of the bottom
        if (scrollTop + windowHeight >= documentHeight - 100) {
            // Check for any unactivated elements that are visible
            const unactivatedImages = document.querySelectorAll('img.lazy-fade:not(.fade-in)');
            const unactivatedGroups = document.querySelectorAll('.group-fade-in:not(.animate)');

            unactivatedImages.forEach(img => {
                const rect = img.getBoundingClientRect();
                if (rect.top < windowHeight && rect.bottom > 0) {
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.onload = () => img.classList.add('fade-in');
                    }
                }
            });

            unactivatedGroups.forEach(group => {
                const rect = group.getBoundingClientRect();
                if (rect.top < windowHeight && rect.bottom > 0) {
                    group.classList.add('animate');
                }
            });
        }
    }

    // Check on scroll
    window.addEventListener('scroll', checkBottomElements);
    // Check once after load in case page is already at bottom
    setTimeout(checkBottomElements, 500);
});
