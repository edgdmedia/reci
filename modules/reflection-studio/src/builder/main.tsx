import { createRoot } from 'react-dom/client';
import { useBuilderStore } from './store/builderStore';
import App from './components/App';
import type { BlueprintV2 } from '@/types/blueprint';

declare global {
  interface Window {
    RECIStudioBuilderConfig?: {
      blueprint: BlueprintV2;
      previewUrl: string;
      saveEndpoint: string;
      nonce: string;
      postId: number;
    };
  }
}

const container = document.getElementById('reflection-studio-builder');
if (container && window.RECIStudioBuilderConfig) {
  const config = window.RECIStudioBuilderConfig;
  useBuilderStore.getState().loadBlueprint(config.blueprint);

  createRoot(container).render(
    <App
      previewUrl={config.previewUrl}
      saveEndpoint={config.saveEndpoint}
      nonce={config.nonce}
      postId={config.postId}
    />
  );
}
