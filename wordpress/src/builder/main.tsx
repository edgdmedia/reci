import { createRoot } from 'react-dom/client';
import App from './components/App';
import { useBuilderStore } from './store/builderStore';
import type { Blueprint } from '../../types/blueprint';

async function renderApp() {
  const rootEl = document.getElementById('reci-builder-root');
  if (!rootEl) return;

  // Hydrate store from existing blueprint JSON (if editing an existing post)
  const blueprintInput = document.getElementById('reci-builder-blueprint') as HTMLInputElement | null;
  if (blueprintInput?.value) {
    try {
      const bp = JSON.parse(blueprintInput.value) as Blueprint;
      useBuilderStore.getState().loadBlueprint(bp);
    } catch {
      // malformed JSON — start fresh
    }
  }

  createRoot(rootEl).render(<App />);
}

renderApp();
