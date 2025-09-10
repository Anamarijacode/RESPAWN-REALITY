document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.rating-stars .star');
    const ratingInput = document.getElementById('rating-value');

    // Provjeravamo postoji li već odabrana ocjena u inputu (ako postoji, postavite aktivne zvjezdice)
    const currentRating = ratingInput.value;

    // Ako postoji trenutna ocjena, postavite odgovarajući broj zvjezdica kao aktivne
    if (currentRating) {
        stars.forEach((star) => {
            if (star.getAttribute('data-value') <= currentRating) {
                star.classList.add('active');
            }
        });
    }

    stars.forEach((star) => {
        star.addEventListener('click', () => {
            stars.forEach((s) => s.classList.remove('active')); // Ukloni sve aktivne zvjezdice
            star.classList.add('active'); // Dodaj aktivnu zvjezdicu
            ratingInput.value = star.getAttribute('data-value'); // Spremi vrijednost ocjene u input
        });
    });
});
