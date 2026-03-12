// Utility for opening the native WordPress media library picker.
// window.wp.media is provided by WordPress admin — not available in tests.

declare global {
  interface Window {
    wp?: {
      media: (options: {
        title: string;
        button: { text: string };
        multiple: boolean;
      }) => {
        on: (event: string, cb: () => void) => void;
        open: () => void;
        state: () => { get: (key: string) => { first: () => { toJSON: () => { url: string } } } };
      };
    };
  }
}

export function openWpMedia(onChange: (url: string) => void): () => void {
  return () => {
    if (!window.wp?.media) return;

    const frame = window.wp.media({
      title: 'Select Image',
      button: { text: 'Use this image' },
      multiple: false,
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      onChange(attachment.url);
    });

    frame.open();
  };
}
