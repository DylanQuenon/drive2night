const addImage = document.querySelector('#add-image')
addImage.addEventListener('click',()=>{
    // compter combien j'ai d form-group pour les indices ex: annonce_image_0_url
    const widgetCounter = document.querySelector("#widgets-counter")
    const index = +widgetCounter.value // le + permet de transformer en nombre, value rends tjrs un string 
    const annonceImages = document.querySelector("#car_images")
    // recup le prototype dans la div

    const prototype = annonceImages.dataset.prototype.replace(/__name__/g, index) // drapeau g pour indiqur que l'on va le faire plusieurs fois 
    annonceImages.insertAdjacentHTML('beforeend', prototype)
    widgetCounter.value = index+1
    handleDeleteButtons() //  pour mettre à jour la table deletes
})

const updateCounter = () => {
    const count = document.querySelectorAll("#car_images div.form-group").length
    document.querySelector("#widgets-counter").value = count 
}

const handleDeleteButtons = () => {
    let deletes = document.querySelectorAll("button[data-action='delete']")
    deletes.forEach(button => {
        button.addEventListener('click', ()=>{
            const target = button.dataset.target
            const elementTarget = document.querySelector(target)
            if(elementTarget){
                elementTarget.remove() // supprimer l'éléménet
            }
        })
    })

}

updateCounter()
handleDeleteButtons()

//bandeau qui défile au scroll sur la page home
const scenes = document.querySelectorAll('.scene');
scenes.forEach(scene => {
  const elements = scene.querySelectorAll('.element');
  const transitionDelay = 100; // Délai en millisecondes entre les dates

  const moveElements = (percent) => {
    elements.forEach((element, elementIndex) => {
      
      if(element.getAttribute('delta-x')){
        const deltaX = parseFloat(element.getAttribute('delta-x'));
        element.style.transform = `translateX(${deltaX * percent * 3}vw)`;
      }

      if(element.getAttribute('data-transi')){
        const transit = parseFloat(element.getAttribute('data-transi'));
        // console.log(percent,transit)
        const opacity = (percent>=transit)? 1 : 0;
        element.style.opacity = opacity;
      }

    
   
    });
  };

  document.addEventListener('scroll', () => {
    const start = (window.scrollY + window.innerHeight) - scene.offsetTop;
    const length = window.innerHeight + scene.offsetHeight;
    const percent = Math.max(0, Math.min(1, start / length));
    moveElements(percent);
    // console.log(percent)
  });
});