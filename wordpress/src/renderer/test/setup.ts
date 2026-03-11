import '@testing-library/jest-dom';

// scrollIntoView is not implemented in jsdom
window.HTMLElement.prototype.scrollIntoView = () => {};

// GSAP ScrollTrigger requires matchMedia — provide a stub for jsdom
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: (query: string) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: () => {},
    removeListener: () => {},
    addEventListener: () => {},
    removeEventListener: () => {},
    dispatchEvent: () => false,
  }),
});
