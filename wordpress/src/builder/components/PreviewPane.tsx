import { useEffect, useRef, useState } from 'react';
import type { BuilderConfig, ReflectionSystemBlueprint } from '../../types/blueprint';

declare global {
  interface Window {
    RECIReflectionBuilderConfig?: BuilderConfig;
  }
}

interface Props {
  blueprint: ReflectionSystemBlueprint;
}

export default function PreviewPane({ blueprint }: Props) {
  const config = window.RECIReflectionBuilderConfig;
  const [previewUrl, setPreviewUrl] = useState<string>('');
  const [status, setStatus] = useState<'idle' | 'syncing' | 'ready' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState<string>('');
  const timerRef = useRef<number | null>(null);

  useEffect(() => {
    if (!config?.postId || !config.previewEndpoint || !config.previewNonce) {
      return;
    }

    if (timerRef.current) {
      window.clearTimeout(timerRef.current);
    }

    timerRef.current = window.setTimeout(async () => {
      setStatus('syncing');
      setErrorMessage('');
      try {
        const response = await fetch(config.previewEndpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.previewNonce,
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            post_id: config.postId,
            blueprint,
          }),
        });

        const data = await response.json();
        if (!response.ok || !data?.preview_url) {
          throw new Error(data?.message || 'Unable to generate preview.');
        }

        setPreviewUrl(data.preview_url);
        setStatus('ready');
      } catch (error) {
        setStatus('error');
        setErrorMessage(error instanceof Error ? error.message : 'Unable to generate preview.');
      }
    }, 450);

    return () => {
      if (timerRef.current) {
        window.clearTimeout(timerRef.current);
      }
    };
  }, [blueprint, config?.postId, config?.previewEndpoint, config?.previewNonce]);

  return (
    <aside className="flex w-[42rem] max-w-[48%] shrink-0 flex-col overflow-hidden bg-gray-950 text-white">
      <div className="flex items-center justify-between border-b border-white/10 px-4 py-3">
        <div>
          <h2 className="text-sm font-semibold">Preview</h2>
          <p className="text-xs text-gray-400">Live render from the new reflection system.</p>
        </div>
        <span className="text-xs text-gray-400">
          {status === 'syncing' && 'Updating...'}
          {status === 'ready' && 'Up to date'}
          {status === 'error' && 'Preview error'}
          {status === 'idle' && 'Waiting'}
        </span>
      </div>
      {status === 'error' ? (
        <div className="m-4 rounded-lg border border-red-400/30 bg-red-500/10 p-4 text-sm text-red-100">{errorMessage}</div>
      ) : null}
      <div className="flex-1 overflow-hidden bg-white">
        {previewUrl ? (
          <iframe title="Reflection preview" src={previewUrl} className="h-full w-full border-0 bg-white" />
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-gray-500">Preview will appear here.</div>
        )}
      </div>
    </aside>
  );
}
