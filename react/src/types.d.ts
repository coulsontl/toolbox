declare global {
  interface Window {
    JSONTools: { mount: (el: HTMLElement) => void; }
  }
}

export {} 