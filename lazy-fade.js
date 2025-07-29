document.addEventListener('DOMContentLoaded', () => {
    // Function to check if element is in the last child of body
    function isInLastChildOfBody(element) {
        const bodyChildren = document.body.children;
        if (bodyChildren.length === 0) return false;
        const lastChild = bodyChildren[bodyChildren.length - 1];
        return lastChild.contains(element);
    }

    // Filter out elements in the last child of body
    const allLazyImages = document.querySelectorAll('img.lazy-fade');
    const allGroups = document.querySelectorAll('.group-fade-in');

    const lazyImages = Array.from(allLazyImages).filter(img => !isInLastChildOfBody(img));
    const groups = Array.from(allGroups).filter(group => !isInLastChildOfBody(group));

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
});
