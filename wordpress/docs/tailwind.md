# Tailwind Workflow (WordPress Theme)

From the `wordpress` theme directory:

```bash
npm install
npm run dev
```

- `npm run dev` watches template/source files and writes to `assets/css/tailwind.css`.
- `npm run build` creates a minified production build.

## Files

- `tailwind.config.js` – scan paths + theme extensions
- `postcss.config.js` – Tailwind + Autoprefixer
- `src/tailwind.css` – Tailwind entry file
- `assets/css/tailwind.css` – compiled output

## Enqueue

`inc/theme-setup.php` loads `assets/css/tailwind.css` automatically when file exists.
