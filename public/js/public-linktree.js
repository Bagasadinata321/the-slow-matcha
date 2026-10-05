document.addEventListener('DOMContentLoaded', () => {
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const accordion = this.parentElement;
            const body = this.nextElementSibling;
            const isOpen = accordion.classList.contains('open');

            // Tutup semua accordion yang sedang terbuka
            document.querySelectorAll('.link-accordion').forEach(item => {
                item.classList.remove('open');
                const accordionBody = item.querySelector('.accordion-body');
                if (accordionBody) {
                    accordionBody.style.maxHeight = null;
                }
            });

            // Buka accordion yang diklik jika sebelumnya tertutup
            if (!isOpen) {
                accordion.classList.add('open');
                body.style.maxHeight = body.scrollHeight + "px";
            }
        });
    });
});