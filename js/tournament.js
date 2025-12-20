document.addEventListener('DOMContentLoaded', function() {
        const allFilterButtons = document.querySelectorAll('.filter-btn');
        const tournamentSections = document.querySelectorAll('.tournament-section');
        const rankingsSection = document.querySelector('.rankings-section');
        const rankingsTitle = document.querySelector('.rankings-section h2');

        function updateDisplay(filter) {
            // Logic to change title
            if (filter === 'all') {
                if (rankingsTitle) rankingsTitle.innerHTML = '<i class="fas fa-list-ul"></i> All Tournaments';
            } else {
                if (rankingsTitle) rankingsTitle.innerHTML = '<i class="fas fa-trophy"></i> Global Team Rankings';
            }

            // Logic to show/hide sections
            if (filter === 'all') {
                if (rankingsSection) rankingsSection.style.display = 'block';
                tournamentSections.forEach(section => {
                    section.style.display = 'block';
                });
            } 
            else if (filter === 'rankings_only') {
                if (rankingsSection) rankingsSection.style.display = 'block';
                tournamentSections.forEach(section => {
                    section.style.display = 'none';
                });
            } 
            else { // Filter for a specific game
                if (rankingsSection) rankingsSection.style.display = 'none';
                tournamentSections.forEach(section => {
                    section.style.display = (section.getAttribute('data-id') === filter) ? 'block' : 'none';
                });
            }
        }

        // Add click listeners to buttons
        allFilterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                allFilterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');
                updateDisplay(filter);
            });
        });

        // Set initial state on page load
        const initialActiveButton = document.querySelector('.filter-btn.active');
        if (initialActiveButton) {
            updateDisplay(initialActiveButton.getAttribute('data-filter'));
        }

        // Toggle score visibility
        const toggleScoreButtons = document.querySelectorAll('.toggle-score');
        toggleScoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const scoreElement = this.closest('.match-details').querySelector('.score');
                if (scoreElement) {
                    const isHidden = scoreElement.style.display === 'none' || scoreElement.style.display === '';
                    scoreElement.style.display = isHidden ? 'inline-block' : 'none';
                }
            });
        });
    });