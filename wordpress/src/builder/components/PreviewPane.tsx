import { useEffect, useRef, useState } from 'react';
import type { BuilderConfig, ReflectionSystemBlueprint, ReflectionSystemComponent } from '../../types/blueprint';

declare global {
  interface Window {
    RECIReflectionBuilderConfig?: BuilderConfig;
  }
}

interface Props {
  blueprint: ReflectionSystemBlueprint;
  selectedChapterId?: string | null;
  lastEditedChapter?: ReflectionSystemComponent | null;
  previewReloadKey?: number;
}

interface PreviewMessage {
  type: 'preview-ready';
}

export default function PreviewPane({ blueprint, selectedChapterId, lastEditedChapter, previewReloadKey = 0 }: Props) {
  const config = window.RECIReflectionBuilderConfig;
  const [previewUrl, setPreviewUrl] = useState<string>('');
  const [status, setStatus] = useState<'idle' | 'syncing' | 'ready' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState<string>('');
  const iframeRef = useRef<HTMLIFrameElement>(null);
  const iframeReadyRef = useRef(false);
  const timerRef = useRef<number | null>(null);

  // Full load for initial render and structural changes.
  useEffect(() => {
    if (!config?.postId || !config.previewEndpoint || !config.previewNonce) {
      return;
    }

    const doFullLoad = async () => {
      console.log('[builder] full preview load, reloadKey:', previewReloadKey);
      setStatus('syncing');
      setErrorMessage('');
      try {
        const body: Record<string, unknown> = {
          post_id: config.postId,
          blueprint,
        };
        if (selectedChapterId) {
          body.selected_chapter_id = selectedChapterId;
        }
        const response = await fetch(config.previewEndpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.previewNonce,
          },
          credentials: 'same-origin',
          body: JSON.stringify(body),
        });
        const data = await response.json();
        if (!response.ok || !data?.preview_url) {
          throw new Error(data?.message || 'Unable to generate preview.');
        }
        setPreviewUrl(data.preview_url);
        iframeReadyRef.current = false;
        setStatus('ready');
      } catch (error) {
        setStatus('error');
        setErrorMessage(error instanceof Error ? error.message : 'Unable to generate preview.');
      }
    };

    doFullLoad();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [previewReloadKey]);

  // Listen for postMessage from preview iframe.
  useEffect(() => {
    function onMessage(event: MessageEvent) {
      const data = event.data as PreviewMessage;
      if (data?.type === 'preview-ready') {
        console.log('[builder] preview-ready received');
        iframeReadyRef.current = true;
      }
    }
    window.addEventListener('message', onMessage);
    return () => window.removeEventListener('message', onMessage);
  }, []);

  // Send scroll-to-chapter via postMessage (no server call).
  useEffect(() => {
    if (!selectedChapterId || !iframeReadyRef.current) return;
    console.log('[builder] sending scroll-to-chapter:', selectedChapterId);
    const iframe = iframeRef.current;
    if (iframe?.contentWindow) {
      iframe.contentWindow.postMessage({ type: 'scroll-to-chapter', chapterId: selectedChapterId }, '*');
    }
  }, [selectedChapterId]);

  // Debounced surgical update for chapter props changes.
  useEffect(() => {
    if (!lastEditedChapter || !iframeReadyRef.current) return;
    if (!config?.postId || !config.previewEndpoint || !config.previewNonce) return;

    if (timerRef.current) {
      window.clearTimeout(timerRef.current);
    }

    timerRef.current = window.setTimeout(async () => {
      console.log('[builder] rendering chapter:', lastEditedChapter?.id);
      setStatus('syncing');
      try {
        const response = await fetch('/wp-json/reci/v1/render-chapter', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.previewNonce,
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            chapter: lastEditedChapter,
            post_id: config.postId,
          }),
        });
        const data = await response.json();
        if (!response.ok || typeof data?.html !== 'string') {
          throw new Error(data?.message || 'Unable to render chapter.');
        }

        const chapterId = lastEditedChapter.id;
        console.log('[builder] sending update-chapter:', chapterId);
        if (chapterId) {
          const iframe = iframeRef.current;
          if (iframe?.contentWindow) {
            iframe.contentWindow.postMessage({ type: 'update-chapter', chapterId, html: data.html }, '*');
          }
        }
        setStatus('ready');
      } catch (error) {
        console.error('[builder] chapter render failed:', error);
        setStatus('error');
        setErrorMessage(error instanceof Error ? error.message : 'Unable to render chapter.');
      }
    }, 450);

    return () => {
      if (timerRef.current) {
        window.clearTimeout(timerRef.current);
      }
    };
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [lastEditedChapter]);

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
          <iframe
            ref={iframeRef}
            title="Reflection preview"
            src={previewUrl}
            className="h-full w-full border-0 bg-white"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-gray-500">Preview will appear here.</div>
        )}
      </div>
    </aside>
  );
}
