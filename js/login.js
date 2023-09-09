const formInscription = document.getElementById('formInscription');
        const formConnexion = document.getElementById('formConnexion');
        const switchToLogin = document.getElementById('switchToLogin');
        const switchToSignup = document.getElementById('switchToSignup');

        switchToLogin.addEventListener('click', () => {
            formInscription.style.display = 'none';
            formConnexion.style.display = 'flex';
        });

        switchToSignup.addEventListener('click', () => {
            formInscription.style.display = 'flex';
            formConnexion.style.display = 'none';
        });