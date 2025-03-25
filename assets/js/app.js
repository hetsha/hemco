let nextButton = document.getElementById('next');
let prevButton = document.getElementById('prev');
let carousel = document.querySelector('.carousel');
let listHTML = document.querySelector('.carousel .list');
let seeMoreButtons = document.querySelectorAll('.seeMore');
let backButton = document.getElementById('back');

nextButton.onclick = function () {
    showSlider('next');
}
prevButton.onclick = function () {
    showSlider('prev');
}

let unAcceppClick;
const showSlider = (type) => {
    nextButton.style.pointerEvents = 'none';
    prevButton.style.pointerEvents = 'none';

    carousel.classList.remove('next', 'prev');
    let items = document.querySelectorAll('.carousel .list .item');
    if (type === 'next') {
        listHTML.appendChild(items[0]);
        carousel.classList.add('next');
    } else {
        listHTML.prepend(items[items.length - 1]);
        carousel.classList.add('prev');
    }
    clearTimeout(unAcceppClick);
    unAcceppClick = setTimeout(() => {
        nextButton.style.pointerEvents = 'auto';
        prevButton.style.pointerEvents = 'auto';
    }, 2000)
}

// Auto-slide every 3 seconds
let autoSlide = setInterval(() => showSlider('next'), 5000);

// Pause auto-slide when hovering over the carousel
carousel.addEventListener('mouseenter', () => clearInterval(autoSlide));
carousel.addEventListener('mouseleave', () => autoSlide = setInterval(() => showSlider('next'), 3000));

seeMoreButtons.forEach((button) => {
    button.onclick = function () {
        carousel.classList.remove('next', 'prev');
        carousel.classList.add('showDetail');
        clearInterval(autoSlide);  // Stop auto-slide when showing details
    }
});
backButton.onclick = function () {
    carousel.classList.remove('showDetail');
    autoSlide = setInterval(() => showSlider('next'), 3000);  // Resume auto-slide
}
