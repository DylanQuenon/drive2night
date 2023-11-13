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

//bandeau qui défile au scroll sur la page home
