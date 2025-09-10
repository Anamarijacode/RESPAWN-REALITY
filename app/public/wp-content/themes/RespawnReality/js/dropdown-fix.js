/* document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.navbar-nav .dropdown').forEach(dropdown => {
        const submenu = dropdown.querySelector('.dropdown-menu');

        if (!submenu) return; // Ako nema podmenija, izađi iz funkcije

        // Desktop hover event
        dropdown.addEventListener('mouseenter', function () {
            submenu.style.display = 'block'; // Privremeno prikaži
            const rect = submenu.getBoundingClientRect();
            submenu.style.display = ''; // Vrati na default

            // Provjera granica prozora
            if (rect.right > window.innerWidth) {
                submenu.classList.add('check-boundary'); // Pomakni ulijevo
            } else {
                submenu.classList.remove('check-boundary'); // Drži s desne strane
            }

            // Prikaži podmeni
            submenu.classList.add('show');
        });

        dropdown.addEventListener('mouseleave', function () {
            // Sakrij podmeni kad kursor napusti dropdown
            submenu.classList.remove('show');
        });

        // Mobile click event
        dropdown.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                e.preventDefault(); // Spriječi defaultnu akciju linka
                submenu.classList.toggle('show'); // Prikaz/skrivanje podmenija
            }
        });
    });
});
 */