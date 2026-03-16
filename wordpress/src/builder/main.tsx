import { createRoot } from 'react-dom/client';
import App from './components/App';
import { useBuilderStore, initialBlueprint } from './store/builderStore';
import type { BuilderConfig } from '../types/blueprint';

declare global {
  interface Window {
    RECIReflectionBuilderConfig?: BuilderConfig;
  }
}

async function renderApp() {
  const rootEl = document.getElementById('reci-builder-root');
  if (!rootEl) return;

  const config = window.RECIReflectionBuilderConfig;
  const blueprintInput = document.getElementById('reci-builder-blueprint') as HTMLInputElement | null;
  const fallback = config?.defaultBlueprint ?? initialBlueprint;

  if (blueprintInput?.value) {
    try {
      const blueprint = JSON.parse(blueprintInput.value) as Record<string, unknown>;
      useBuilderStore.getState().loadBlueprint(blueprint);
    } catch {
      useBuilderStore.getState().loadBlueprint(fallback);
    }
  } else {
    useBuilderStore.getState().loadBlueprint(fallback);
  }

  createRoot(rootEl).render(<App />);
}

renderApp();
