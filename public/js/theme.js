const root = document.documentElement


function theme_clair(){
    root.style.setProperty('--dark-principal', '#DCE2EC')
    root.style.setProperty('--text-prinicpal', '#000')
    root.style.setProperty('--dark-secondary', '#F5F7FA')
    root.style.setProperty('--text-secondary', '#000')
    root.style.setProperty('--color-fifty', '#000')
}

function theme_sombre(){
    root.style.setProperty('--dark-principal', '#0f172a')
    root.style.setProperty('--text-prinicpal', '#fff')
    root.style.setProperty('--dark-secondary', '#1e293b')
    root.style.setProperty('--text-secondary', '#fff')
    root.style.setProperty('--color-fifty', 'grey')
}

changer_theme()

let theme = document.querySelector('.theme')
theme.addEventListener('click', function(){
    const i = document.querySelector('.theme .bi-sun')
    // const j = document.querySelector('.theme .bi-moon')
    if(i != undefined){
        localStorage.setItem('themes', 'clair')
        this.innerHTML = "<i class='bi bi-moon'></i> Sombre"
        changer_theme()
    }else{
        localStorage.setItem('themes', 'sombre')
        this.innerHTML = "<i class='bi bi-sun'></i> Clair"
        changer_theme()
    }
})

function changer_theme(){
    if(localStorage.getItem('themes') == 'sombre'){
        theme_sombre()
    }else{
        theme_clair()
    }
}