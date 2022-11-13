const openBtn = document.querySelector('.navbar-mobile-button');

openBtn.addEventListener('click', () => {
    openPopup();
});

const openPopup = () => {
    const popup = document.querySelector('.navbar-mobile-wrap');

    popup.classList.add('active');
    handleOpenedPopupEvents(popup);
}

const handleOpenedPopupEvents = (popup) => {
    const closeBtn = document.querySelector('.navbar-mobile-close');
    let acc = document.querySelector(".navbar-mobile-accordion");

    acc.addEventListener("click", toggleClass);

    closeBtn.addEventListener('click', () => {
        acc.removeEventListener('click', toggleClass)
        popup.classList.remove('active');
    })
}

const toggleClass = () => {
    let panel = document.querySelector('.navbar-mobile-panel');
    panel.classList.toggle('active');
}