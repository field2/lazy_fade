document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img.lazy-fade');
    const groups = document.querySelectorAll('.group-fade-in');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.onload = () => img.classList.add('fade-in');
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '100px',
        threshold: 0.1
    });

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
});
