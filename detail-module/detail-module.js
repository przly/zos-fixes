const GUEST_BLUR_VAR = '--blur-height';

document.querySelectorAll('.detail-module--guest').forEach((moduleEl) => {
  const body = moduleEl.querySelector('.detail-module__body');
  const firstItem = moduleEl.querySelector('.detail-module__body .detail-module__item');

  if (!body || !firstItem) {
    return;
  }

  const update = () => {
    // Blur covers the lower half of the visible text: from the cut-off edge
    // up to the midpoint between where the text starts and the cut-off.
    // __body is position: relative, so offsetTop is measured from its top.
    const visibleText = Math.max(body.clientHeight - firstItem.offsetTop, 0);
    moduleEl.style.setProperty(GUEST_BLUR_VAR, `${visibleText / 2}px`);
  };

  update();

  if (typeof ResizeObserver !== 'undefined') {
    new ResizeObserver(update).observe(body);
  }

  window.addEventListener('resize', debounce(update, 150));
});
