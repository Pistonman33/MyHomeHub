<script>
    function darkModeData() {
        return {
            darkMode: localStorage.getItem('darkMode') === 'true', // récupère le choix
            toggle() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode); // sauvegarde le choix
            }
        }
    }
</script>
