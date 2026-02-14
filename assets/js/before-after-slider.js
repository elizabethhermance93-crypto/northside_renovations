document.addEventListener('DOMContentLoaded', () => {
  const sliders = document.querySelectorAll('.before-after-slider');

  sliders.forEach((slider) => {
    const before = slider.querySelector('.before-image');
    const beforeImage = before ? before.querySelector('img') : null;
    const resizer = slider.querySelector('.resizer');

    if (!before || !beforeImage || !resizer) {
      return;
    }

    let active = false;

    const setBeforeWidth = () => {
      const width = slider.offsetWidth;
      beforeImage.style.width = `${width}px`;
      before.style.width = `${width / 2}px`;
      resizer.style.left = `${width / 2}px`;
    };

    setBeforeWidth();
    window.addEventListener('resize', setBeforeWidth);

    const slideIt = (pageX) => {
      const sliderRect = slider.getBoundingClientRect();
      const offset = Math.max(0, Math.min(pageX - sliderRect.left, sliderRect.width));
      before.style.width = `${offset}px`;
      resizer.style.left = `${offset}px`;
    };

    const startResize = () => {
      active = true;
      resizer.classList.add('resize');
    };

    const stopResize = () => {
      active = false;
      resizer.classList.remove('resize');
    };

    resizer.addEventListener('mousedown', startResize);
    document.body.addEventListener('mouseup', stopResize);
    document.body.addEventListener('mouseleave', stopResize);
    document.body.addEventListener('mousemove', (e) => {
      if (!active) return;
      slideIt(e.pageX);
      e.preventDefault();
    });

    resizer.addEventListener('touchstart', startResize, { passive: true });
    document.body.addEventListener('touchend', stopResize);
    document.body.addEventListener('touchcancel', stopResize);
    document.body.addEventListener('touchmove', (e) => {
      if (!active) return;
      const touch = e.changedTouches[e.changedTouches.length - 1];
      slideIt(touch.pageX);
      e.preventDefault();
    }, { passive: false });
  });
});
