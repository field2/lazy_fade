document.addEventListener("DOMContentLoaded", () => {
  const images = document.querySelectorAll("img.lazy-fade");

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.onload = () => img.classList.add("fade-in");
        obs.unobserve(img);
      }
    });
  }, {
    rootMargin: "100px",
    threshold: 0.1
  });

  images.forEach(img => observer.observe(img));
});
