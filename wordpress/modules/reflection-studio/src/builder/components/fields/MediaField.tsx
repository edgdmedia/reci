interface Props { label: string; value: string; onChange(v: string): void; }
export default function MediaField({ label, value, onChange }: Props) {
  function openMedia() {
    if (typeof window === 'undefined' || !(window as { wp?: { media?: (opts: unknown) => { on(e: string, cb: () => void): void; open(): void; state(): { get(k: string): { first(): { toJSON(): { url: string } } } } } } }).wp?.media) {
      const url = prompt('Enter image URL:') ?? '';
      if (url) onChange(url);
      return;
    }
    const frame = (window as { wp: { media: (opts: unknown) => { on(e: string, cb: () => void): void; open(): void; state(): { get(k: string): { first(): { toJSON(): { url: string } } } } } } }).wp.media({ title: label, multiple: false });
    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      onChange(attachment.url);
    });
    frame.open();
  }
  return (
    <div className="rs-field rs-field--media">
      <span className="rs-field__label">{label}</span>
      {value && <img src={value} alt="" className="rs-field__media-preview" />}
      <button type="button" onClick={openMedia} className="rs-field__media-btn">
        {value ? 'Change image' : 'Select image'}
      </button>
      {value && <button type="button" onClick={() => onChange('')} className="rs-field__media-clear">Remove</button>}
    </div>
  );
}
